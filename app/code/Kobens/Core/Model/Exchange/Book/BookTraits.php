<?php

namespace Kobens\Core\Model\Exchange\Book;

trait BookTraits
{
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
     * {@inheritDoc}
     * @see \Kobens\Core\Model\Exchange\Book\BookInterface::getExchange()
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
                $this->getQuoteCurrency()->getCacheIdentifier(),
                'book'
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
     * @return array
     */
    public function getBook()
    {
        $book = unserialize($this->cache->load($this->getBookCacheKey()));
        if ($book === false) {
            throw new \Exception('Book Not Initialized');
        }
        return ;
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
