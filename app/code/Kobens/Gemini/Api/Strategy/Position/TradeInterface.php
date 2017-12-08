<?php

namespace Kobens\Gemini\Api\Strategy\Position;

interface TradeInterface
{
    const TRADE_ID              = 'trade_id';
    const STRATEGY_ID           = 'strategy_id';
    const TIMESTAMPMS           = 'timestampms';
    const QUOTE_AMOUNT          = 'quote_amount';
    const BASE_AMOUNT           = 'base_amount';
    const BPS                   = 'bps';
    const FEE_AMOUNT            = 'fee_amount';
}