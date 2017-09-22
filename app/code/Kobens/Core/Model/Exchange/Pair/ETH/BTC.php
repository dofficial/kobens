<?php

namespace Kobens\Core\Model\Exchange\Pair\ETH;

/**
 * @category    \Kobens
 * @package     \Kobens\Core
 */
class BTC extends \Kobens\Core\Model\Exchange\Pair\AbstractPair
{
    const PAIR = 'ETH/BTC';

    /**
     * Constructor
     *
     * @param \Kobens\Core\Model\Currency\Virtual\ETH $baseCurrency
     * @param \Kobens\Core\Model\Currency\Virtual\BTC $quoteCurrency
     */
    public function __construct(
        \Kobens\Core\Model\Currency\Virtual\ETH $baseCurrency,
        \Kobens\Core\Model\Currency\Virtual\BTC $quoteCurrency
    ) {
        parent::__construct($baseCurrency, $quoteCurrency);
    }

}
