<?php

namespace Kobens\Gemini\Model\Order\BTC;

class USD extends \Magento\Framework\Model\AbstractModel {

    /**
     * Mock constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Kobens\Gemini\Model\ResourceModel\Order');
    }

}