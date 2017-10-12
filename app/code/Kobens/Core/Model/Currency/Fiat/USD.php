<?php

namespace Kobens\Core\Model\Currency\Fiat;

class USD extends AbstractFiat
{
    const CURRENCY_NAME = 'US Dollar';
    const MAIN_UNIT = 'Dollar';
    const MAIN_UNIT_ABBREVIATION = 'USD';
    const SUB_UNIT = 'Cent';
    const DENOMINATION = 2;
    const CACHE_IDENTIFIER = 'usd';
    const PAIR_IDENTIFIER = 'usd';

    /**
     * {@inheritDoc}
     * @see \Kobens\Core\Model\Currency\CurrencyInterface::getCurrencyName()
     */
    public function getCurrencyName()
    {
        return self::CURRENCY_NAME;
    }

    /**
     * {@inheritDoc}
     * @see \Kobens\Core\Model\Currency\CurrencyInterface::getMainUnitName()
     */
    public function getMainUnitName()
    {
        return self::MAIN_UNIT;
    }

    public function getMainUnitAbbreviation()
    {
        return self::MAIN_UNIT_ABBREVIATION;
    }

    /**
     * {@inheritDoc}
     * @see \Kobens\Core\Model\Currency\CurrencyInterface::getSubunitName()
     */
    public function getSubunitName()
    {
        return self::SUB_UNIT;
    }

    /**
     * {@inheritDoc}
     * @see \Kobens\Core\Model\Currency\CurrencyInterface::getSubunitDenomination()
     */
    public function getSubunitDenomination()
    {
        return self::DENOMINATION;
    }

    /**
     * {@inheritDoc}
     * @see \Kobens\Core\Model\Currency\CurrencyInterface::getCacheIdentifier()
     */
    public function getCacheIdentifier()
    {
        return self::CACHE_IDENTIFIER;
    }

    /**
     * {@inheritDoc}
     * @see \Kobens\Core\Model\Currency\CurrencyInterface::getPairIdentity()
     */
    public function getPairIdentity()
    {
        return self::PAIR_IDENTIFIER;
    }

}
