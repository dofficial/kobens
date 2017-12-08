<?php

namespace Kobens\Gemini\Model\ResourceModel;

use \Kobens\Gemini\Api\StrategyInterface;

class Strategy extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    
    protected function _construct()
    {
        $this->_init('kobens_gemini_strategy', StrategyInterface::STRATEGY_ID);
    }
    
}