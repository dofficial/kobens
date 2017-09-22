<?php

namespace Kobens\Gemini\Model\Exchange\Book\ETH;

use Kobens\Core\Model\Exchange\Book\AbstractBook;

class BTC extends \Kobens\Core\Model\Exchange\Book\AbstractBook
{
    /**
     * Time (in seconds) to consider a book closed if
     * no updates have occurred between now and last update.
     *
     * @var integer
     */
    protected $bookExpiration = 10;

    public function __construct(
        \Kobens\Gemini\Model\Exchange $exchange
    ) {
        parent::__construct($exchange, $exchange->getPair('eth/btc'));
    }
}