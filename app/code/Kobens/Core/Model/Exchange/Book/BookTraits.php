<?php

namespace Kobens\Core\Model\Exchange\Book;

trait BookTraits
{
    /**
     * Time (in seconds) to consider a book closed if
     * no updates have occurred between now and last update.
     *
     * @var integer
     */
    protected $bookExpiration = 5;

    /**
     * The exchange market's order book
     *
     * @var array
     */
    protected $book;

    /**
     * @var \Kobens\Core\Model\Exchange\ExchangeInterface
     */
    protected $exchange;

    /**
     * @var \Kobens\Core\Model\Exchange\Pair\PairInterface
     */
    protected $pair;

    /**
     * @var \Magento\Framework\Cache\FrontendInterface
     */
    protected $cache;

    /**
     * @var string
     */
    protected $cacheKeyBook;

    /**
     * @var string
     */
    protected $cacheKeyHeartbeat;

    /**
     * @var string
     */
    protected $cacheKeyLastTrade;

    /**
     * @return \Kobens\Core\Model\Exchange\ExchangeInterface
     */
    public function getExchange()
    {
        return $this->exchange;
    }

    /**
     * Return the cache key for the current book
     *
     * @return string
     */
    protected function getBookCacheKey()
    {
        if (!$this->cacheKeyBook) {
            $this->cacheKeyBook = implode('::', [
                'kobens',
                $this->getExchange()->getCacheKey(),
                'market-book',
                $this->pair->getPairSymbol(),
            ]);
        }
        return $this->cacheKeyBook;
    }

    /**
     * @return string
     */
    protected function getLastTradeCacheKey()
    {
        if (!$this->cacheKeyLastTrade) {
            $this->cacheKeyLastTrade= implode('::', [
                'kobens',
                $this->getExchange()->getCacheKey(),
                $this->getBaseCurrency()->getCacheIdentifier(),
                $this->getQuoteCurrency()->getCacheIdentifier(),
                'last_trade'
            ]);
        }
        return $this->cacheKeyLastTrade;
    }

    /**
     * @return string
     */
    protected function getHeartbeatCacheKey()
    {
        if (!$this->cacheKeyHeartbeat) {
            $this->cacheKeyLastTrade= implode('::', [
                'kobens',
                $this->getExchange()->getCacheKey(),
                $this->getBaseCurrency()->getCacheIdentifier(),
                $this->getQuoteCurrency()->getCacheIdentifier(),
                'heartbeat'
            ]);
        }
        return $this->cacheKeyHeartbeat;
    }

    /**
     * Return the market's order book.
     *
     * @throws \Kobens\Core\Exception\ClosedBookException
     * @return mixed
     */
    public function getBook()
    {
        $isAlive = $this->cache->test($this->getBookCacheKey());
        if (!$isAlive) {
            throw new \Kobens\Core\Exception\ClosedBookException('Market book is closed.');
        } elseif (time() - $isAlive >= $this->bookExpiration) {
            throw new \Kobens\Core\Exception\ClosedBookException('Market book has expired.');
        }
        return unserialize($this->cache->load($this->getBookCacheKey()));
    }

    /**
     * @return \Kobens\Core\Model\Exchange\Pair\PairInterface
     */
    public function getPair()
    {
        return $this->pair;
    }

    /**
     * @return \Kobens\Core\Model\Currency\CurrencyInterface
     */
    public function getBaseCurrency()
    {
        return $this->pair->getBaseCurrency();
    }

    /**
     * @return \Kobens\Core\Model\Currency\CurrencyInterface
     */
    public function getQuoteCurrency()
    {
        return $this->pair->getQuoteCurrency();
    }

}
