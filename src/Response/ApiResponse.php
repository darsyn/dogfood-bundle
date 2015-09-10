<?php
namespace Darsyn\Bundle\DogfoodBundle\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @author Zander Baldwin <hello@zanderbaldwin.com>
 */
class ApiResponse extends JsonResponse
{
    /**
     * @access protected
     * @var mixed
     */
    protected $originalData;

    /**
     * Constructor
     *
     * @access public
     * @param mixed $data
     * @param integer $status
     * @param array $headers
     */
    public function __construct($data = null, $status = 200, $headers = array())
    {
        $this->originalData = $data;
        parent::__construct($data, $status, $headers);
    }

    /**
     * Get Data
     *
     * @access public
     * @return mixed
     */
    public function getData()
    {
        return $this->originalData;
    }
}
