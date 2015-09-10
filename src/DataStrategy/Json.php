<?php
namespace Darsyn\Bundle\DogfoodBundle\DataStrategy;

/**
 * @author Zander Baldwin <hello@zanderbaldwin.com>
 */
class Json implements DataStrategyInterface
{
    /**
     * Get Content Type
     *
     * @access public
     * @return string
     */
    public function getContentType()
    {
        return 'application/json';
    }

    /**
     * Flatten
     *
     * @access public
     * @return string
     */
    public function flatten($data)
    {
        return json_encode($data);
    }
}
