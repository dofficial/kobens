<?php

namespace Kobens\Core\Model\Exchange\Book\Trade;

class Trade implements TradeInterface
{
    protected $makerSide;

    protected $quantity;

    protected $price;

    protected $timestampms;

    public function __construct(
        $makerSide,
        $quantity,
        $price,
        $timestampms
    ) {
        $this->makerSide = $makerSide;
        $this->quantity = $quantity;
        $this->price = $price;
        $this->timestampms = $timestampms;
    }

    /**
     * @return string   ask|bid
     */
    public function getMakerSide()
    {
        return $this->makerSide;
    }

    /**
     * Get the quantity of base currency traded.
     *
     * @return decimal
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Return the currency quote amount that the base currency was traded at.
     *
     * @return decimal
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Return the timestamp for the trade.
     */
    public function getTimestamp()
    {
        return $this->timestampms;
    }

}
