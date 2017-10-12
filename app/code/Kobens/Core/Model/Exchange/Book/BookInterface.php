<?php

/**
 * TODO: Re-asses the setters here. We have an abstract book, and
 * the exchange specific abstract book extends core abstract book...
 * no public setters should be necessary. Book internallcy
 * sets the data and public functions fetch it for other
 * processes.
 */

namespace Kobens\Core\Model\Exchange\Book;

interface BookInterface
{
    /**
     * Return the Exchange model for the current book.
     *
     * @return \Kobens\Core\Model\Exchange\ExchangeInterface
     */
    public function getExchange();

    /**
     * Return the Pair model for the current book.
     *
     * @return \Kobens\Core\Model\Exchange\Pair\PairInterface
     */
    public function getPair();

    /**
     * Return the base currency for the current book.
     *
     * @return \Kobens\Core\Model\Currency\CurrencyInterface
     */
    public function getBaseCurrency();

    /**
     * Return the quote currency for the current quote.
     *
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
     * Return the current asking price on the order book.
     *
     * @return decimal
     */
    public function getAskPrice();

    /**
     * Return the current bid price
     *
     * @return decimal
     */
    public function getBidPrice();

    /**
     * @return decimal;
     */
    public function getSpread();

}
