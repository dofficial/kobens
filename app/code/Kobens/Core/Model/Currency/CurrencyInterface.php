<?php

namespace Kobens\Core\Model\Currency;

interface CurrencyInterface
{
    /**
     * Return the full name of the currency.
     * Example: US Dollar | Bitcoin | Ethereaum
     *
     * @return string
     */
    public function getCurrencyName();

    /**
     * Return the name of the currency's main unit.
     * Example: Dollar | Bitcoin | Ether
     *
     * @return string
     */
    public function getMainUnitName();

    /**
     * Return the abbreviation for the main unit
     * Example: USD | BTC | ETH
     *
     * @return string
     */
    public function getMainUnitAbbreviation();

    /**
     * Return the name of the currency's subunit.
     * Example: Cent | Satoshi | Wei
     *
     * @return string
     */
    public function getSubunitName();

    /**
     * How many decimal points behind zero does the currency's subunit go.
     *
     * @return integer
     */
    public function getSubunitDenomination();

    /**
     * Return the string value to use in cache identifiers
     *
     * @return string
     */
    public function getCacheIdentifier();

    /**
     * Return the string value to use for this currency in a pair
     *
     * @return string
     */
    public function getPairIdentity();

    /**
     * Return the currency type (Fiat or Crypto)
     *
     * @return string
     */
    public function getCurrencyType();
}