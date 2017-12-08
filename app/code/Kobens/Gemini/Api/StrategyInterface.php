<?php

namespace Kobens\Gemini\Api;

interface StrategyInterface
{
    const   MAIN_TABLE              = 'kobens_gemini_strategy';
    
    const   STRATEGY_ID             = 'strategy_id';
    const   PAIR_SYMBOL             = 'pair_symbol';
    const   OPEN_PRICE              = 'open_price';
    const   OPEN_AMOUNT             = 'open_amount';
    const   SELL_GAIN_PERCENT       = 'sell_gain_percent';
    const   SELL_HODL_PERCENT       = 'sell_hodle_percent';
    const   CLOSE_PRICE             = 'close_price';
    const   CLOSE_AMOUNT            = 'close_amount';
    const   HODL_AMOUNT             = 'hodle_amount';
}
