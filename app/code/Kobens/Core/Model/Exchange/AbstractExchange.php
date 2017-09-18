<?php

namespace Kobens\Core\Model\Exchange;

abstract class AbstractExchange implements ExchangeInterface
{
    /**
     * @var \Kobens\Core\Model\Exchange\Pair\PairInterface[]
     */
    protected $pairs = [];

    public function __construct(
        array $pairs = []
    ) {
        $this->addPairs($pairs);
    }

    /**
     * Add currency pair to the exchange
     *
     * @param \Kobens\Core\Model\Exchange\Pair\PairInterface[] $pairs
     * @throws \Exception
     */
    protected function addPairs($pairs)
    {
        if (!is_array($pairs)) {
            $pairs = [$pairs];
        }
        foreach ($pairs as $pair) {
            if (!$pair instanceof \Kobens\Core\Model\Exchange\Pair\PairInterface) {
                throw new \Exception('Invalid Pair Interface');
            }
            $base = $pair->getBaseCurrency()->getPairIdentity();
            $quote = $pair->getQuoteCurrency()->getPairIdentity();
            $this->pairs[$base.'/'.$quote] = $pair;
        }
    }

    /**
     * @param string $key
     * @return \Kobens\Core\Model\Exchange\Pair\PairInterface
     */
    public function getPair($key)
    {
        if (!isset($this->pairs[$key])) {
            throw new \Exception ('Currency Pair "'.$key.'" not found on exchange');
        }
        return $this->pairs[$key];
    }
}