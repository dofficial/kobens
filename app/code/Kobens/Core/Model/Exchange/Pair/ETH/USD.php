<?php

namespace Kobens\Core\Model\Exchange\Pair\BTC;

/**
 * @category    \Kobens
 * @package     \Kobens\Core
 */
class USD extends \Kobens\Core\Model\Exchange\Pair\AbstractPair
{
    /**
     * Constructor
     *
     * @param \Kobens\Core\Model\Currency\Virtual\ETH $baseCurrency
     * @param \Kobens\Core\Model\Currency\Fiat\USD $quoteCurrency
     */
    public function __construct(
        \Kobens\Core\Model\Currency\Virtual\ETH $baseCurrency,
        \Kobens\Core\Model\Currency\Fiat\USD $quoteCurrency
    ) {
        parent::__construct($baseCurrency, $quoteCurrency);
    }

}
