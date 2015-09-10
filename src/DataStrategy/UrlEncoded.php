<?php
namespace Darsyn\Bundle\DogfoodBundle\DataStrategy;

/**
 * @author Zander Baldwin <hello@zanderbaldwin.com>
 */
class UrlEncoded implements DataStrategyInterface
{
    /**
     * Get Content Type
     *
     * @access public
     * @return string
     */
    public function getContentType()
    {
        return 'application/x-www-form-urlencoded';
    }

    /**
     * Flatten
     *
     * @access public
     * @return string
     */
    public function flatten($data)
    {
        return http_build_query($data);
    }
}
