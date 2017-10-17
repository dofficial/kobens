<?php

namespace Kobens\Core\Model\Currency\Crypto;

abstract class AbstractCrypto implements \Kobens\Core\Model\Currency\CurrencyInterface
{
    const CURRENCY_TYPE = 'crypto';

    public function getCurrencyType()
    {
        return self::CURRENCY_TYPE;
    }

}
