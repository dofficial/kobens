<?php

namespace Kobens\Core\Model\Exchange\Book;

abstract class AbstractBook implements BookInterface
{
    use BookTraits;

    /**
     * @param \Kobens\Core\Model\Exchange\Pair\PairInterface $pairInterface
     * @param \Kobens\Core\Model\Exchange\ExchangeInterface $exchangeInterface
     * @param \Magento\Framework\Cache\FrontendInterface $cache
     */
    public function __construct(
        \Kobens\Core\Model\Exchange\ExchangeInterface $exchangeInterface,
        \Kobens\Core\Model\Exchange\Pair\PairInterface $pairInterface,
        \Magento\Framework\Cache\FrontendInterface $cacheInterface
    ) {
        $this->exchange = $exchangeInterface;
        $this->pair = $pairInterface;
        $this->cache = $cacheInterface;
        $this->_construct();
    }

    protected function _construct()
    {
        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Kobens\Core\Model\Exchange\Book\BookInterface::getLastTrade()
     */
    public function getLastTrade()
    {
        return unserialize($this->cache->load($this->getLastTradeCacheKey()));
    }

    /**
     * Get the remaining amount on the book for the given maker side and quote.
     *
     * @param string $makerSide
     * @param float|string $quote
     * @return float
     */
    public function getRemaining($makerSide, $quote)
    {
        $book = $this->getBook();
        $quote = string($quote);
        if (isset($book[$makerSide][$quote])) {
            return $book[$makerSide][$quote];
        }
        return floatval(0);
    }

    /**
     * {@inheritDoc}
     * @see \Kobens\Core\Model\Exchange\Book\BookInterface::getAskPrice()
     */
    public function getAskPrice()
    {
        \Zend_Debug::dump($this->getBook());exit;
        $prices = $this->getBook()['ask'];
        ksort($prices);
        return floatval(array_keys($prices)[0]);
    }

    /**
     * {@inheritDoc}
     * @see \Kobens\Core\Model\Exchange\Book\BookInterface::getBidPrice()
     */
    public function getBidPrice()
    {
        $prices = $this->getBook()['bid'];
        ksort($prices);
        $vals = array_keys($prices);
        return floatval(end($vals));
    }

    /**
     * {@inheritDoc}
     * @see \Kobens\Core\Model\Exchange\Book\BookInterface::getSpread()
     */
    public function getSpread()
    {
        $ask = $this->getAskPrice();
        $bid = $this->getBidPrice();
        $decimals = $this->getQuoteCurrency()->getSubunitDenomination();
        $spread = number_format($ask - $bid, $decimals);
        return floatVal($spread);
    }

}
