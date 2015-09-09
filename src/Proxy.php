<?php
namespace Darsyn\Bundle\DogfoodingBundle;

use Darsyn\Bundle\DogfoodBundle\Event\RequestEvent;
use Darsyn\Bundle\DogfoodBundle\Event\ResponseEvent;
use Darsyn\Bundle\DogfoodBundle\Event\RouteEvent;

class Proxy
{
    private $requestStack;
    private $router;
    private $eventDispatcher;
    private $logger;

    public function __construct(
        RequestStack $requestStack,
        Router $router,
        EventDispatcher $eventDispatcher = null,
        LoggerInterface $logger = null
    ) {
        $this->requestStack = $requestStack;
        $this->router = $router;
        $this->eventDispatcher = $eventDispatcher;
        $this->logger = $logger;
    }

    protected function dispatchEvent($name, EventInterface $event)
    {
        if ($this->eventDispatcher instanceof EventDispatcher) {
            $this->eventDispatcher->dispatch($name, $event);
        }
    }

    public function dogfood($method, $path, $data)
    {
        $route = new Route($path);
        $route->setMethods([$method]);
        return $this->dogfoodRouteObject($route, [], $data);
    }

    public function dogfoodRoute($name, array $parameters, $data)
    {
        $collection = $this->container->get('router')->getRouteCollection();
        $route = $collection->get($name);
        if (!$route instanceof Route) {
            throw new RouteNotFoundException(sprintf(
                'Unable to generate a URL for the named route "%s" as such route does not exist.',
                $name
            ));
        }
        return $this->dogfoodRouteObject($route, $parameters, $data);
    }

    private function dogfoodRouteObject(Route $route, $parameters, $data)
    {
        $request = $this->requestStack->getCurrentRequest()->duplicate();
        $this->dispatchEvent('darsyn_dogfood.route', $event = new RouteEvent($route));
        $this->mergeRouteIntoRequest($event->getRoute(), $request);
        return $this->dogfoodRequest($request);
    }

    private function mergeRouteIntoRequest(Route $route, Request $request)
    {
        $method = $route->getMethods();
        if (count($method) !== 1) {
            throw new AmbiguousHttpMethodException;
        }
        $request->setMethod(array_unshift($method));
        $method = array_unshift($method);

        // Generate URL from
    }

    public function dogfoodRequest(Request $request)
    {
        $this->eventDispatcher->dispatch('darsyn_dogfood.request', $event = new RequestEvent($request));
        $response = $this->container->get('http_kernel')->handle(
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
        $this->eventDispatcher->dispatch('darsyn_dogfood.response', new ResponseEvent($response));
        return $event->getResponse();
    }
}
