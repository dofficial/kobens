<?php

namespace Kobens\Gemini\Model\Exchange\Book\BTC;

use Kobens\Core\Model\Exchange\Book\AbstractBook;

class USD extends \Kobens\Core\Model\Exchange\Book\AbstractBook
{
    public function __construct(
        \Kobens\Gemini\Model\Exchange $exchange,
        \Kobens\Core\Model\Exchange\Pair\BTC\USD $pair,
        \Kobens\Gemini\Model\Cache\MarketData\BTC\USD $cache
    ) {
        parent::__construct($exchange, $pair, $cache);
    }
}