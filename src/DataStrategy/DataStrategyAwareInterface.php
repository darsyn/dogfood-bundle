<?php
namespace Darsyn\Bundle\DogfoodBundle\DataStrategy;

/**
 * @author Zander Baldwin <hello@zanderbaldwin.com>
 */
interface DataStrategyAwareInterface
{
    /**
     * Set Data Strategy
     *
     * @access public
     * @param \Darsyn\Bundle\DogfoodBundle\DataStrategy\DataStrategyInterface $dataStrategy
     */
    public function setDataStrategy(DataStrategyInterface $dataStrategy);
}
