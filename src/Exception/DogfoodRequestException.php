<?php
namespace Darsyn\Bundle\DogfoodBundle\Exception;

/**
 * @author Zander Baldwin <hello@zanderbaldwin.com>
 */
class DogfoodRequestException extends \Exception
{
    /**
     * Constructor
     *
     * @access public
     * @param \Exception $previous
     * @param string $message
     * @param integer $code
     */
    public function __construct(\Exception $previous, $message = 'An internal application request failed.', $code = 0)
    {
        parent::__construct($message, $code, $previous);
    }
}
