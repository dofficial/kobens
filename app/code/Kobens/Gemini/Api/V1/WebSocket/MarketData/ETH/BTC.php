<?php

namespace Kobens\Gemini\Api\V1\WebSocket\MarketData\ETH;

class BTC extends \Kobens\Gemini\Api\V1\WebSocket\MarketData\AbstractBookKeeper
{
    /**
     * Time (in seconds) to consider a book closed if
     * no updates have occurred between now and last update.
     *
     * @var integer
     */
    protected $bookExpiration = 10;

    /**
     * @param \Kobens\Gemini\Model\Exchange $exchange
     */
    public function __construct(
        \Kobens\Gemini\Model\Exchange $exchange
    ) {
        parent::__construct($exchange, $exchange->getPair('eth/btc'));
    }

}

