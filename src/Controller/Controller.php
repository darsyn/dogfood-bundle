<?php
namespace Darsyn\Bundle\DogfoodBundle\Controller;

use Darsyn\Bundle\DogfoodBundle\DependencyInjection\DogfoodAwareInterface;
use Darsyn\Bundle\DogfoodBundle\Proxy\ProxyInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as FrameworkController;
use Symfony\Component\Routing\Route;

/**
 * @author Zander Baldwin <hello@zanderbaldwin.com>
 */
abstract class Controller extends FrameworkController implements DogfoodAwareInterface
{
    /**
     * @access private
     * @var \Darsyn\Bundle\DogfoodingBundle\Proxy
     */
    private $proxy;

    /**
     * Set Dogfood Proxy
     *
     * @access public
     * @param \Darsyn\Bundle\DogfoodBundle\Proxy\ProxyInterface $proxy
     * @return void
     */
    public function setDogfoodProxy(ProxyInterface $proxy)
    {
        $this->proxy = $proxy;
    }

    /**
     * Get Proxy
     *
     * @access private
     * @return \Darsyn\Bundle\DogfoodingBundle\Proxy
     */
    private function getProxy()
    {
        if (!$this->proxy instanceof ProxyInterface) {
            $this->setDogfoodProxy($this->container->get('darsyn_dogfood.proxy'));
        }
        return $this->proxy;
    }

    /**
     * Dogfood
     *
     * @access public
     * @param string $method
     * @param string $path
     * @param mixed $data
     * @return mixed
     */
    public function dogfood($method, $path, $data = null)
    {
        return $this->getProxy()->dogfood($method, $path, $data);
    }

    /**
     * Dogfood Route
     *
     * @access public
     * @param string $name
     * @param array $parameters
     * @param mixed $data
     * @return mixed
     */
    public function dogfoodRoute($name, array $parameters = [], $data = null)
    {
        return $this->getProxy()->dogfoodRoute($name, $parameters, $data);
    }

    /**
     * Dogfood Request
     *
     * @access public
     * @param Request $request
     * @return mixed
     */
    public function dogfoodRequest(Request $request)
    {
        return $this->getProxy()->dogfoodRequest($request);
    }
}
