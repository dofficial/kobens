<?php

namespace Kobens\Gemini\Model\ResourceModel\Position\BTC\USD;

/**
 * Collection resource for positions with the BTC\USD pair on the Gemini exchange.
 *
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Kobens\Gemini\Model\Position\Position\BTC\USD',
            'Kobens\Gemini\Model\ResourceModel\Position\BTC\USD'
        );
    }
}