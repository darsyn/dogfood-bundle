<?php
namespace Darsyn\Bundle\DogfoodingBundle\Controller;

use Darsyn\Bundle\DogfoodingBundle\Proxy;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as FrameworkController;
use Symfony\Component\Routing\Route;

abstract class Controller extends FrameworkController
{
    /**
     * @access private
     * @var \Darsyn\Bundle\DogfoodingBundle\Proxy
     */
    private $proxy;

    /**
     * Get Proxy
     *
     * @access private
     * @return \Darsyn\Bundle\DogfoodingBundle\Proxy
     */
    private function getProxy()
    {
        if (!$proxy instanceof Proxy) {
            $this->proxy = $this->container->get('darsyn_dogfooding.proxy');
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
    public function dogfood($method, $path, $data)
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
    public function dogfoodRoute($name, array $parameters, $data)
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
