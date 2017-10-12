<?php

namespace Kobens\Core\Model\Currency\Crypto;

class BTC extends AbstractCrypto
{
    const CURRENCY_NAME = 'Bitcoin';
    const MAIN_UNIT = 'Bitcoin';
    const MAIN_UNIT_ABBREVIATION = 'BTC';
    const SUB_UNIT = 'Satoshi';
    const DENOMINATION = 8;
    const CACHE_IDENTIFIER = 'btc';
    const PAIR_IDENTIFIER = 'btc';

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
