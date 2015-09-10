<?php
namespace Darsyn\Bundle\DogfoodBundle\DependencyInjection;

use Darsyn\Bundle\DogfoodBundle\Proxy\ProxyInterface;

/**
 * @author Zander Baldwin <hello@zanderbaldwin.com>
 */
interface DogfoodAwareInterface
{
    /**
     * Set Dogfood Proxy
     *
     * @access public
     * @param \Darsyn\Bundle\DogfoodBundle\Proxy\ProxyInterface $proxy
     * @return void
     */
    public function setDogfoodProxy(ProxyInterface $proxy);
}
