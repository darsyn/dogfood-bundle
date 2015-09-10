<?php
namespace Darsyn\Bundle\DogfoodBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Zander Baldwin <hello@zanderbaldwin.com>
 */
class ResponseEvent extends Event
{
    /**
     * @access private
     * @var \Symfony\Component\HttpFoundation\Response
     */
    private $response;

    /**
     * Constructor
     *
     * @access public
     * @param Symfony\Component\HttpFoundation\Request $response
     */
    public function __construct(Response $response)
    {
        $this->setResponse($response);
    }

    /**
     * Get Response
     *
     * @access public
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Set Response
     *
     * @access public
     * @param Symfony\Component\HttpFoundation\Response $response
     * @return void
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }
}
