<?php
namespace Darsyn\Bundle\DogfoodBundle\Proxy;

use Darsyn\Bundle\DogfoodBundle\DataStrategy\DataStrategyAwareInterface;
use Darsyn\Bundle\DogfoodBundle\DataStrategy\DataStrategyInterface;
use Darsyn\Bundle\DogfoodBundle\Event;
use Darsyn\Bundle\DogfoodBundle\Exception;
use Symfony\Component\EventDispatcher\Event as EventInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;
use Psr\Log\LoggerInterface;

/**
 * @author Zander Baldwin <hello@zanderbaldwin.com>
 */
class Proxy implements ProxyInterface, DataStrategyAwareInterface
{
    /**
     * @access private
     * @var \Symfony\Component\HttpKernel\HttpKernelInterface
     */
    private $kernel;

    /**
     * @access private
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    /**
     * @access private
     * @var \Symfony\Component\Routing\Router
     */
    private $router;

    /**
     * @access private
     * @var \Symfony\Component\EventDispatcher\EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @access private
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @access private
     * @var \Darsyn\Bundle\DogfoodBundle\DataStrategy\DataStrategyAwareInterface
     */
    private $dataStrategy;

    /**
     * Constructor
     *
     * @access public
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
     * @param \Symfony\Component\Routing\RouterInterface $router
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface|null $eventDispatcher
     * @param \Psr\Log\LoggerInterface|null $logger
     */
    public function __construct(
        HttpKernelInterface $kernel,
        RequestStack $requestStack,
        RouterInterface $router,
        EventDispatcherInterface $eventDispatcher = null,
        LoggerInterface $logger = null
    ) {
        $this->kernel = $kernel;
        $this->requestStack = $requestStack;
        $this->router = $router;
        $this->eventDispatcher = $eventDispatcher;
        $this->logger = $logger;
    }

    /**
     * Set Data Strategy
     *
     * @access public
     * @param \Darsyn\Bundle\DogfoodBundle\DataStrategy\DataStrategyAwareInterface $dataStrategy
     * @return void
     */
    public function setDataStrategy(DataStrategyInterface $dataStrategy)
    {
        if ($this->logger instanceof LoggerInterface) {
            $this->logger->debug(sprintf(
                'Using data strategy provided by "%s" for dogfooding request.',
                get_class($dataStrategy)
            ));
        }
        $this->dataStrategy = $dataStrategy;
    }

    /**
     * Get Data Strategy
     *
     * @access protected
     * @return \Darsyn\Bundle\DogfoodBundle\DataStrategy\DataStrategyAwareInterface
     */
    protected function getDataStrategy()
    {
        if (!$this->dataStrategy instanceof DataStrategyInterface) {
            throw new \RuntimeException('No data strategy has been set.');
        }
        return $this->dataStrategy;
    }

    /**
     * Dispatch Event
     *
     * @access protected
     * @param string $name
     * @param \Symfony\Component\EventDispatcher\Event $event
     * @return void
     */
    protected function dispatchEvent($name, EventInterface $event)
    {
        if ($this->eventDispatcher instanceof EventDispatcher) {
            $this->eventDispatcher->dispatch($name, $event);
        }
    }

    /**
     * Dogfood
     *
     * @access public
     * @param string $method
     * @param string $path
     * @param mixed $data
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function dogfood($method, $path, $data = null)
    {
        $request = $this->createRequest($method, $path, $data);
        return $this->dogfoodRequest($request);
    }

    /**
     * Dogfood from Route
     *
     * @access public
     * @param string $name
     * @param array $parameters
     * @param mixed $data
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function dogfoodRoute($name, array $parameters = [], $data = null)
    {
        $collection = $this->router->getRouteCollection();
        $route = $collection->get($name);
        if (!$route instanceof Route) {
            throw new RouteNotFoundException(sprintf(
                'Unable to generate a URL for the named route "%s" as such route does not exist.',
                $name
            ));
        }

        $method = $route->getMethods();
        if (count($method) !== 1) {
            throw new Exception\AmbiguousHttpMethodException($name);
        }

        $request = $this->createRequest(
            array_shift($method),
            $this->router->generate($name, $parameters),
            $data
        );
        return $this->dogfoodRequest($request);
    }

    /**
     * Create Request
     *
     * @access private
     * @param string $method
     * @param string $path
     * @param mixed $data
     * @return [type]
     */
    private function createRequest($method, $path, $data = null)
    {
        $current = $this->requestStack->getCurrentRequest();

        $parameters = [];
        $content = null;
        switch (strtoupper($method)) {
            case Request::METHOD_POST:
            case Request::METHOD_PUT:
            case Request::METHOD_DELETE:
            case Request::METHOD_PATCH:
                if ($this->logger instanceof LoggerInterface) {
                    $this->logger->debug('Flattening data to use as request body.');
                }
                $content = $this->getDataStrategy()->flatten($data);
                break;
            default:
                $parameters = (array) $data;
                break;
        }

        $request = Request::create(
            $path,
            $method,
            $parameters,
            $current->cookies->all(),
            $current->files->all(),
            array_merge($current->server->all(), [
                'CONTENT_TYPE' => $this->getDataStrategy()->getContentType(),
                'CONTENT_LENGTH' => strlen($content),
            ]),
            $content
        );
        $request->setMethod($method);
        return $request;
    }

    /**
     * Dogfood from Request
     *
     * @access public
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function dogfoodRequest(Request $request)
    {
        $this->eventDispatcher->dispatch('darsyn_dogfood.request', $event = new Event\RequestEvent($request));
        if ($this->logger instanceof LoggerInterface) {
            $this->logger->info('Getting HTTP kernel to handle dogfooding request.');
        }
        try {
            $response = $this->kernel->handle(
                $event->getRequest(),
                // Although we are creating a subrequest within the framework, we are actually emulating a request
                // across microservices and want it to be treated as such (we don't want functionality such as the
                // security component disabled).
                HttpKernelInterface::MASTER_REQUEST,
                // If exceptions are thrown within the API pseudo-microservice then we want to handle them ourselves, rather
                // than the Kernel catching them. There is no point in catching an exception in order to generate a response
                // containing error codes and messages when we are going to immediately going to reverse that process.
                false
            );
        } catch (\Exception $e) {
            throw new Exception\DogfoodRequestException($e);
        }
        if ($this->logger instanceof LoggerInterface) {
            $this->logger->debug('Dogfooding request successfully returned response.');
        }
        $this->eventDispatcher->dispatch('darsyn_dogfood.response', $event = new Event\ResponseEvent($response));
        return $event->getResponse();
    }
}
