<?php
namespace Darsyn\Bundle\DogfoodBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Routing\Route;

/**
 * @author Zander Baldwin <hello@zanderbaldwin.com>
 */
class RouteEvent extends Event
{
    /**
     * @access private
     * @var \Symfony\Component\Routing\Route
     */
    private $route;

    /**
     * Constructor
     *
     * @access public
     * @param Symfony\Component\Routing\Route $route
     */
    public function __construct(Route $route)
    {
        $this->setRoute($route);
    }

    /**
     * Get Route
     *
     * @access public
     * @return \Symfony\Component\Routing\Route
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Set Route
     *
     * @access public
     * @param Symfony\Component\Routing\Route $route
     * @return void
     */
    public function setRoute(Route $route)
    {
        $this->route = $route;
    }
}
