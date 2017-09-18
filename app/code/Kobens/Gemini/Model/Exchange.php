<?php

namespace Kobens\Gemini\Model;

class Exchange extends \Kobens\Core\Model\Exchange\AbstractExchange
{
    const CACHE_KEY = 'gemini';

    /**
     * @return string
     */
    public function getCacheKey()
    {
        return self::CACHE_KEY;
    }

}
