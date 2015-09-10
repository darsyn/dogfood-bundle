<?php
namespace Darsyn\Bundle\DogfoodBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Zander Baldwin <hello@zanderbaldwin.com>
 */
class RequestEvent extends Event
{
    /**
     * @access private
     * @var \Symfony\Component\HttpFoundation\Request
     */
    private $request;

    /**
     * Constructor
     *
     * @access public
     * @param Symfony\Component\HttpFoundation\Request $request
     */
    public function __construct(Request $request)
    {
        $this->setRequest($request);
    }

    /**
     * Get Request
     *
     * @access public
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Set Request
     *
     * @access public
     * @param Symfony\Component\HttpFoundation\Request $request
     * @return void
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }
}
