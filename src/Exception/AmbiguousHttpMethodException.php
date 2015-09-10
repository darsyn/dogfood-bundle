<?php
namespace Darsyn\Bundle\DogfoodBundle\Exception;

/**
 * @author Zander Baldwin <hello@zanderbaldwin.com>
 */
class AmbiguousHttpMethodException extends \Exception
{
    protected $message = 'Could not determine which HTTP method to use for route "%s".';

    /**
     * Constructor
     *
     * @access public
     * @param string $route
     */
    public function __construct($route)
    {
        parent::__construct(sprintf($this->message, $route));
    }
}
