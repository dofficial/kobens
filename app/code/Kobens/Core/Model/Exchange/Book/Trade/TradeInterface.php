<?php

namespace Kobens\Core\Model\Exchange\Book\Trade;

interface TradeInterface
{
    /**
     * @return string   ask|bid
     */
    public function getMakerSide();

    /**
     * Get the quantity of base currency traded.
     *
     * @return decimal
     */
    public function getQuantity();

    /**
     * Return the currency quote amount that the base currency was traded at.
     *
     * @return decimal
     */
    public function getPrice();

    /**
     * Return the timestamp for the trade.
     */
    public function getTimestamp();

}