<?php

namespace Kobens\Gemini\Api\V1\WebSocket\MarketData\BTC;

class USD extends \Kobens\Gemini\Api\V1\WebSocket\MarketData\AbstractBookKeeper
{
    /**
     * Constructor
     *
     * @param \Kobens\Gemini\Model\Exchange $exchange
     * @param \Kobens\Gemini\Model\Cache $cache
     */
    public function __construct(
        \Kobens\Gemini\Model\Exchange $exchange,
        \Kobens\Gemini\Model\Cache $cache
    ) {
        parent::__construct($exchange, $exchange->getPair('btc/usd'), $cache);
    }

}
