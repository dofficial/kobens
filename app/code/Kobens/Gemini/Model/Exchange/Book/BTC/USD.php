<?php

namespace Kobens\Gemini\Model\Exchange\Book\BTC;

use Kobens\Core\Model\Exchange\Book\AbstractBook;

class USD extends \Kobens\Core\Model\Exchange\Book\AbstractBook
{
    public function __construct(
        \Kobens\Gemini\Model\Exchange $exchange
    ) {
        parent::__construct($exchange, $exchange->getPair('btc/usd'));
    }
}