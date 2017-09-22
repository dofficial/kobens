<?php

namespace Kobens\Gemini\Model\Exchange\Book\ETH;

use Kobens\Core\Model\Exchange\Book\AbstractBook;

class BTC extends \Kobens\Core\Model\Exchange\Book\AbstractBook
{
    /**
     * Constructor
     *
     * @param \Kobens\Gemini\Model\Exchange $exchange
     */
    public function __construct(
        \Kobens\Gemini\Model\Exchange $exchange
    ) {
        parent::__construct($exchange, $exchange->getPair('eth/btc'));
    }
}