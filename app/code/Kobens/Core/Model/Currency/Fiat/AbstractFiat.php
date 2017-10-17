<?php

namespace Kobens\Core\Model\Currency\Fiat;

abstract class AbstractFiat implements \Kobens\Core\Model\Currency\CurrencyInterface
{
    const CURRENCY_TYPE = 'fiat';

    public function getCurrencyType()
    {
        return self::CURRENCY_TYPE;
    }

}
