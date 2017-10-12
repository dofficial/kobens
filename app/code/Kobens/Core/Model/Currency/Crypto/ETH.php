<?php

namespace Kobens\Core\Model\Currency\Crypto;

class ETH extends AbstractCrypto
{
    const CURRENCY_NAME = 'Ethereum';
    const MAIN_UNIT = 'Ether';
    const MAIN_UNIT_ABBREVIATION = 'ETH';
    const SUB_UNIT = 'Wei';
    const DENOMINATION = 18;
    const CACHE_IDENTIFIER = 'eth';
    const PAIR_IDENTIFIER = 'eth';


    /**
     * {@inheritDoc}
     * @see \Kobens\Core\Model\Currency\CurrencyInterface::getCurrencyName()
     */
    public function getCurrencyName()
    {
        return self::CURRENCY_NAME;
    }

    public function getMainUnitAbbreviation()
    {
        return self::MAIN_UNIT_ABBREVIATION;
    }

    /**
     * {@inheritDoc}
     * @see \Kobens\Core\Model\Currency\CurrencyInterface::getMainUnitName()
     */
    public function getMainUnitName()
    {
        return self::MAIN_UNIT;
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
