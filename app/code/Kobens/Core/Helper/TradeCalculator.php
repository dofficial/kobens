<?php

namespace Kobens\Core\Helper;

class TradeCalculator
{
    /**
     * Return the actual cost of a trade.
     *
     * @param \Kobens\Core\Model\Exchange\Pair\PairInterface $pairInterface
     * @param string $quoteAmount
     * @param string $quotePrice
     */
    public function getTradeCost(
        \Kobens\Core\Model\Exchange\Pair\PairInterface $pairInterface,
        $quoteAmount,
        $quotePrice
    ) {
        if (!is_string($quoteAmount) || !is_string($quotePrice)) {
            throw new \Kobens\Core\Exception\Exception('Monetary values should only be passed as strings');
        }
        $base = $pairInterface->getBaseCurrency();
        $quote = $pairInterface->getQuoteCurrency();
        $baseAmount = bcdiv($quoteAmount, $quotePrice, $base->getSubunitDenomination());
        $presicion = $base->getSubunitDenomination() + $quote->getSubunitDenomination();
        return bcmul($baseAmount, $quotePrice, $presicion);
    }
}
