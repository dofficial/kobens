<?php

namespace Kobens\Gemini\Model\BTC\USD;

class Book
{
    /**
     * Structure:
     *  [
     *      'ask' => [
     *          '5000' => [ // Index is USD price represented in string format
     *              'time' => 1500000000000, // epoc in milliseconds since last update on this price
     *              'qty' => 100 // BTC Available
     *          ],
     *          '5000.01' [ ... ]
     *          // It will skil a unit if there is not one in the book for it.
     *          '5000.03' [ ... ]
     *      ],
     *      'bid' => [
     *
     *      ]
     * @var array
     */
    protected static $book = [
        'ask' => [],
        'bid' => []
    ];

    /**
     * @var array
     */
    protected static $lastTrade = [
        'maker' => null,
        'price' => null,
        'time' => null,
        'amount' => null
    ];

    public static function getBook()
    {
        return self::$book;
    }

    /**
     * @param array $event
     */
    public static function updateBook(\stdClass $event, $time)
    {
        $side = $event->side;
        $price = $event->price;
        $remaining = $event->remaining;

        if (isset(self::$book[$side][$price])) {
            $lastTime = self::$book[$side][$price]['time'];
            if ($lastTime <= $time) {
                if ($remaining == 0) {
                    unset(self::$book[$side][$price]);
                } else {
                    self::_updateBook($side, $price, $remaining, $time);
                }
            }
        } else {
            self::_updateBook($side, $price, $remaining, $time);
        }
    }

    protected static function _updateBook($side, $price, $remaining, $time)
    {
        self::$book[$side][$price] = [
            'remaining' => $remaining,
            'time' => $time,
        ];
    }

    public static function getAskPrice()
    {
        ksort(self::$book['ask']);
        return floatval(array_keys(self::$book['ask'])[0]);
    }

    public static function getBidPrice()
    {
        ksort(self::$book['bid']);
        $vals = array_keys(self::$book['bid']);
        return floatval(end($vals));
    }

    public static function getLastTrade()
    {
        return self::$lastTrade;
    }

    public static function setLastTrade($price, $maker, $amount, $time)
    {
        if (is_null(self::$lastTrade['time']) || self::$lastTrade['time'] < $time) {
            self::$lastTrade['price'] = $price;
            self::$lastTrade['time'] = $time;
            self::$lastTrade['amount'] = $amount;
            self::$lastTrade['maker'] = $maker;

        }
    }
}
