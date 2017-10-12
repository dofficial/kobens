<?php

namespace Kobens\Core\Model\Exchange\Pair\ETH;

/**
 * @category    \Kobens
 * @package     \Kobens\Core
 */
class USD extends \Kobens\Core\Model\Exchange\Pair\AbstractPair
{
    const PAIR = 'ETH/USD';

    /**
     * Constructor
     *
     * @param \Kobens\Core\Model\Currency\Crypto\ETH $baseCurrency
     * @param \Kobens\Core\Model\Currency\Fiat\USD $quoteCurrency
     */
    public function __construct(
        \Kobens\Core\Model\Currency\Crypto\ETH $baseCurrency,
        \Kobens\Core\Model\Currency\Fiat\USD $quoteCurrency
    ) {
        parent::__construct($baseCurrency, $quoteCurrency);
    }

}
