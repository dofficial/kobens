<?php

/**
 * TODO: Re-asses the setters here. We have an abstract book, and
 * the exchange specific abstract book extends core abstract book...
 * no public setters should be necessary. Book internally
 * sets the data and public functions fetch it for other
 * processes.
 */

namespace Kobens\Core\Model\Exchange\Book;

interface BookInterface
{
    /**
     * @return \Kobens\Core\Model\Exchange\ExchangeInterface
     */
    public function getExchange();

    /**
     * @return \Kobens\Core\Model\Exchange\Pair\PairInterface
     */
    public function getPair();

    /**
     * @return \Kobens\Core\Model\Currency\CurrencyInterface
     */
    public function getBaseCurrency();

    /**
     * @return \Kobens\Core\Model\Currency\CurrencyInterface
     */
    public function getQuoteCurrency();

    /**
     * Return the market's order book.
     *
     * @return array
     */
    public function getBook();

    /**
     * Get the remaining amount on the book for the given maker side and quote.
     *
     * @param string $makerSide
     * @param float $quote
     */
    public function getRemaining($makerSide, $quote);

    /**
     * @return \Kobens\Core\Model\Exchange\Book\Trade\TradeInterface
     */
    public function getLastTrade();

    /**
     * @return decimal
     */
    public function getAskPrice();

    /**
     * @return decimal
     */
    public function getBidPrice();

    /**
     * @return decimal;
     */
    public function getSpread();

}
