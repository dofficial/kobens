<?php

namespace Kobens\Core\Model\Exchange\Pair;

interface PairInterface
{
    public function getPairSymbol();

    /**
     * @return \Kobens\Core\Model\Currency\CurrencyInterface
     */
    public function getBaseCurrency();

    /**
     * @return \Kobens\Core\Model\Currency\CurrencyInterface
     */
    public function getQuoteCurrency();

    /**
     * Return the equivilant base currency quantity based
     * off the given quote currency quantity and quote
     * currency rate.
     *
     * @param decimal $quote
     * @param decimal $quote
     * @return decimal
     */
    public function getBaseQty($quoteQty, $quoteRate);

    /**
     * Return the equivilant quote currency quantity based
     * off the given base currency quantity and quote
     * currency rate.
     *
     * @param decimal $quote
     * @param decimal $quote
     * @return decimal
     */
    public function getQuoteQty($baseQty, $quoteRate);

}
