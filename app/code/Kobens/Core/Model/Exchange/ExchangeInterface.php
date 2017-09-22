<?php

namespace Kobens\Core\Model\Exchange;

interface ExchangeInterface
{
    /**
     * @return string
     */
    public function getCacheKey();

    /**
     * @param string $key
     * @return \Kobens\Core\Model\Exchange\Pair\PairInterface
     */
    public function getPair($key);

    /**
     * @return \Magento\Framework\Cache\FrontendInterface
     */
    public function getCache();

}
