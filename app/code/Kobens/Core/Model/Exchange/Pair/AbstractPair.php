<?php

namespace Kobens\Core\Model\Exchange\Pair;

abstract class AbstractPair implements PairInterface
{

    /**
     * @var \Kobens\Core\Model\Currency\CurrencyInterface
     */
    protected $baseCurrency;

    /**
     * @var \Kobens\Core\Model\Currency\CurrencyInterface
     */
    protected $quoteCurrency;

    /**
     * @param Context $context
     */
    public function __construct(
        \Kobens\Core\Model\Currency\CurrencyInterface $baseCurrency,
        \Kobens\Core\Model\Currency\CurrencyInterface $quoteCurrency
    ) {
        $this->baseCurrency = $baseCurrency;
        $this->quoteCurrency = $quoteCurrency;
    }

    /**
     * @param float $quote
     * @param float $quote
     * @return float
     */
    public function getBaseQty($quoteQty, $quoteRate)
    {
        return round(
            $quoteQty / $quoteRate,
            $this->baseCurrency->getSubunitDenomination(),
            PHP_ROUND_HALF_DOWN
        );
    }

    /**
     * Return the equivilant quote currency quantity based
     * off the given base currency quantity and quote
     * currency rate.
     *
     * @param decimal $quote
     * @param decimal $quote
     * @return decimal
     */
    public function getQuoteQty($baseQty, $quoteRate)
    {
        return round(
            $baseQty * $quoteRate,
            $this->baseCurrency->getSubunitDenomination(),
            PHP_ROUND_HALF_UP
        );
    }

    public function getQuoteCurrency()
    {
        return $this->quoteCurrency;
    }

    public function getBaseCurrency()
    {
        return $this->baseCurrency;
    }

}
