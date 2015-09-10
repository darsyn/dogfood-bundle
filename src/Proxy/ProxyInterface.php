<?php
namespace Darsyn\Bundle\DogfoodBundle\Proxy;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Zander Baldwin <hello@zanderbaldwin.com>
 */
interface ProxyInterface
{
    /**
     * Dogfood Request
     *
     * @access public
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return void
     */
    public function dogfoodRequest(Request $request);
}
