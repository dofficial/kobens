<?php

namespace Kobens\Gemini\Api\V1\WebSocket\MarketData\ETH;

class USD extends \Kobens\Gemini\Api\V1\WebSocket\MarketData\AbstractBookKeeper
{
    /**
     * @param \Kobens\Gemini\Model\Exchange $exchange
     */
    public function __construct(
        \Kobens\Gemini\Model\Exchange $exchange
    ) {
        parent::__construct($exchange, $exchange->getPair('eth/usd'));
    }

}

