<?php

namespace Kobens\Core\Model\Exchange\Book\Keeper;

abstract class AbstractKeeper implements KeeperInterface
{
    use \Kobens\Core\Model\Exchange\Book\BookTraits;

    /**
     * @var \Magento\Framework\Cache\FrontendInterface
     */
    protected $cache;

    /**
     * @param \Kobens\Core\Model\Exchange\Pair\PairInterface $pairInterface
     * @param \Kobens\Core\Model\Exchange\ExchangeInterface $exchangeInterface
     * @param \Magento\Framework\Cache\FrontendInterface $cache
     */
    public function __construct(
        \Kobens\Core\Model\Exchange\ExchangeInterface $exchangeInterface,
        \Kobens\Core\Model\Exchange\Pair\PairInterface $pairInterface
    ) {
        $this->cache = $exchangeInterface->getCache();
        $this->exchange = $exchangeInterface;
        $this->pair = $pairInterface;
    }

    /**
     * {@inheritDoc}
     * @see \Kobens\Core\Model\Exchange\Book\Keeper\KeeperInterface::openBook()
     */
    abstract function openBook();

    /**
     * @return boolean
     */
    protected function setPulse()
    {
        return $this->cache->save((string) microtime(true), $this->getHeartbeatCacheKey());
    }

    /**
     * Update the book
     *
     * @param string $makerSide
     * @param float $quote
     * @param float $remaining
     */
    protected function updateBook($makerSide, $quote, $remaining)
    {
        $book = $this->getBook();
        if ($remaining == 0) {
            unset($book[$makerSide][(string) $quote]);
        } else {
            $book[$makerSide][(string) $quote] = $remaining;
        }
        $this->cache->save(serialize($book), $this->getBookCacheKey());
    }

    /**
     * @param array $data
     */
    protected function populateBook(array $book)
    {
        $cacheSaved = $this->cache->save(
            serialize($book),
            $this->getBookCacheKey()
        );
        if (!$cacheSaved) {
            throw new \Exception('Unable to save book to cache.');
        }
    }

    /**
     * {@inheritDoc}
     * @see \Kobens\Core\Model\Exchange\Book\BookInterface::setLastTrade()
     */
    protected function setLastTrade(\Kobens\Core\Model\Exchange\Book\Trade\TradeInterface $trade)
    {
        $this->cache->save(serialize($trade), $this->getLastTradeCacheKey());
    }

}
