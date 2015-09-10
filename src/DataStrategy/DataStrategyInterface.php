<?php
namespace Darsyn\Bundle\DogfoodBundle\DataStrategy;

/**
 * @author Zander Baldwin <hello@zanderbaldwin.com>
 */
interface DataStrategyInterface
{
    /**
     * Get Content Type
     *
     * @access public
     * @return string
     */
    public function getContentType();

    /**
     * Flatten Data
     *
     * Flatten the data into a string according to the content type to be used as the request body.
     *
     * @access public
     * @return string
     */
    public function flatten($data);
}
