<?php

namespace Kobens\Gemini\Model\ResourceModel\Position\BTC;

class USD extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const MAIN_TABLE = 'kobens_gemini_position_btc_usd';

    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE, 'id');
    }

}
