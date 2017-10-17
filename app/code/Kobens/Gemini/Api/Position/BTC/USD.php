<?php

namespace Kobens\Gemini\Api\Position\BTC;

interface USD
{
    const ID                    = 'id';
    const POSITION_OPEN_ID      = 'position_open_id';
    const POSITION_CLOSE_ID     = 'position_close_id';
    const GEMINI_OPEN_ID        = 'gemini_open_id';
    const GEMINI_CLOSE_ID       = 'gemini_close_id';
    const OPEN_CREATED_AT       = 'open_created_at';
    const OPEN_FILLED_AT        = 'open_filled_at';
    const OPEN_PRICE            = 'open_price';
    const OPEN_AMOUNT           = 'open_amount';
    const OPEN_PRECISE_AMOUNT   = 'open_amount_precise';
    const OPEN_BPS              = 'open_bps';
    const OPEN_FEE              = 'open_fee';
    const GOAL                  = 'goal';
    const CLOSE_CREATED_AT      = 'close_created_at';
    const CLOSE_FILLED_AT       = 'close_filled_at';
    const CLOSE_PRICE           = 'close_price';
    const CLOSE_AMOUNT          = 'close_amount';
    const CLOSE_AMOUNT_PRECISE  = 'close_amount_precise';
    const CLOSE_BPS             = 'close_bps';
    const CLOSE_FEE             = 'close_fee';
    const USD_GAIN              = 'usd_gain';
    const BTC_GAIN              = 'btc_gain';
}