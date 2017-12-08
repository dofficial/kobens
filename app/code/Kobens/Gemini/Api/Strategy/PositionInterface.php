<?php

namespace Kobens\Gemini\Api\Strategy;

interface PositionInterface
{
    const MAIN_TABLE            = 'kobens_gemini_strategy_position';
    
    const POSITION_ID           = 'position_id';
    const STRATEGY_ID           = 'strategy_id';
    const API_ID_OPEN           = 'api_id_open';
    const API_ID_CLOSE          = 'api_id_close';
    const GEMINI_ID_OPEN        = 'gemini_id_open';
    const GEMINI_ID_CLOSE       = 'gemini_id_close';
    const CREATED_AT            = 'created_at';
    const STATUS                = 'status';
    const OPEN_BOOKED_AT        = 'open_booked_at';
    const OPEN_FILLED_AT        = 'open_filled_at';
    const OPEN_PRICE            = 'open_price';
    const OPEN_AMOUNT           = 'open_amount';
    const OPEN_AMOUNT_FILLED    = 'open_amount_filled';
    const OPEN_BPS              = 'open_bps';
    const OPEN_FEE              = 'open_fee';
    const CLOSE_BOOKED_AT       = 'close_booked_at';
    const CLOSE_FILLED_AT       = 'close_filled_at';
    const CLOSE_PRICE           = 'close_price';
    const CLOSE_AMOUNT          = 'close_amount';
    const CLOSE_AMOUNT_FILLED   = 'close_amount_filled';
    const CLOSE_BPS             = 'close_bps';
    const CLOSE_FEE             = 'close_fee';
    const QUOTE_GAIN            = 'quote_gain';
    const BASE_GAIN             = 'base_gain';
}