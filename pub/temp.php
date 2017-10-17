<?php

require_once '../vendor/magento/zendframework1/library/Zend/Debug.php';


// Position Preferences
$quoteOpenAmount = '5';  // Amount of quote currency to bid with in each position
$quoteAllowance = '200';    // Amount of quote currency this trading strategy is allowed to work with
$bidFloor = '4700';         // Bid price to start trading strategy at
$bidStep = '2.00';             // Bid price increment step for each subsequent trade position in the strategy
$quoteGoal = '1.025';        // Gain % of quote price for closing the position
$baseRetention = '0.000985';    // Amount of the base currency to keep when closing the position

// Some Variables...
$remainingBalance = $quoteAllowance;
$bidPrice = $startingRange;
$quoteOpenPrice = null;
$positions = [];
$basePrecision = 8;
$quotePrecision = 2;
$fullPrecision = $basePrecision + $quotePrecision;

$quoteOpenPrice = $bidFloor;

while ($remainingBalance >= 0) {

    // Calculate opening position
    $quoteOpenPrice = $quoteOpenPrice ? bcadd($quoteOpenPrice, $bidStep, $quotePrecision) : $bidFloor;
    $baseOpenAmount = bcdiv($quoteOpenAmount, $quoteOpenPrice, $basePrecision);
    $quoteOpenAmountPrecise = bcmul($baseOpenAmount, $quoteOpenPrice, $fullPrecision);

    // Calculate potential opening fees
    $openFee25Bps = bcmul($quoteOpenAmountPrecise, '0.0025', $fullPrecision);
    $openFee15Bps = bcmul($quoteOpenAmountPrecise, '0.0015', $fullPrecision);
    $openFee10Bps = bcmul($quoteOpenAmountPrecise, '0.001', $fullPrecision);

    // If the bid amount with fee is more than our remaining balance then no need to go further
    // rounding is OK here.
    if ($remainingBalance < $quoteOpenAmount + round($openFee25Bps, $quotePrecision, PHP_ROUND_HALF_UP)) {
        break;
    }

    // Amount of base currency to save / sell
    $baseSaveAmount = bcmul($baseOpenAmount, $baseRetention, $basePrecision);
    $baseCloseAmount = bcsub($baseOpenAmount, $baseSaveAmount, $basePrecision);

    // Calculate closing position price
    $quoteClosePrice = bcmul($quoteOpenPrice, $quoteGoal, $quotePrecision);

    // Calculate closing gross yield from closing the trade
    $quoteClosingGross = bcmul($baseCloseAmount, $quoteClosePrice, $fullPrecision);

    // Calculate potential closing fees
    $closeFee25Bps = bcmul($quoteClosingGross, '0.0025', $fullPrecision);
    $closeFee15Bps = bcmul($quoteClosingGross, '0.0015', $fullPrecision);
    $closeFee10Bps = bcmul($quoteClosingGross, '0.001', $fullPrecision);

    // Calculate gains after fees (assumes same fee rate for open and close);
    $quoteGain25Bps = bcsub($quoteClosingGross, $quoteOpenAmountPrecise, $fullPrecision); // Subtract initial investment
    $quoteGain25Bps = bcsub($quoteGain25Bps, $openFee25Bps, $fullPrecision); // Subtract opening fees
    $quoteGain25Bps = bcsub($quoteGain25Bps, $closeFee25Bps, $fullPrecision); // Subtract closing fees

    $quoteGain15Bps = bcsub($quoteClosingGross, $quoteOpenAmountPrecise, $fullPrecision); // Subtract initial investment
    $quoteGain15Bps = bcsub($quoteGain15Bps, $openFee15Bps, $fullPrecision); // Subtract opening fees
    $quoteGain15Bps = bcsub($quoteGain15Bps, $closeFee15Bps, $fullPrecision); // Subtract closing fees

    $quoteGain10Bps = bcsub($quoteClosingGross, $quoteOpenAmountPrecise, $fullPrecision); // Subtract initial investment
    $quoteGain10Bps = bcsub($quoteGain10Bps, $openFee10Bps, $fullPrecision); // Subtract opening fees
    $quoteGain10Bps = bcsub($quoteGain10Bps, $closeFee10Bps, $fullPrecision); // Subtract closing fees

    // Always assume max fee
    $remainingBalance = bcsub($remainingBalance, $quoteOpenAmountPrecise, $fullPrecision); // Opening Bid Amount
    $remainingBalance = bcsub($remainingBalance, $openFee25Bps, $fullPrecision); // Opening Bid Fee

    // lets track our results!
    $positions[] = [
        'open' => [
            'quote_price' => $quoteOpenPrice,
            'quote_amount' => $quoteOpenAmountPrecise,
            'base_amount' => $baseOpenAmount,
//             'fee_25bps' => $openFee25Bps,
//             'fee_15bps' => $openFee15Bps,
//             'fee_10bps' => $openFee10Bps,
        ],
        'close' => [
            'base_amount' => $baseCloseAmount,
            'quote_price' => $quoteClosePrice,
            'quote_swing' => bcsub($quoteClosePrice, $quoteOpenPrice, 2),
//             'quote_gross' => $quoteClosingGross,
//             'fee_25bps' => $closeFee25Bps,
//             'fee_15bps' => $closeFee15Bps,
//             'fee_10bps' => $closeFee10Bps,
        ],
        'net' => [
            'base' => $baseSaveAmount,
            'quote25BPS' => $quoteGain25Bps,
            'quote15BPS' => $quoteGain15Bps,
            'quote10BPS' => $quoteGain10Bps
        ],
        'remaining_balance' => $remainingBalance
    ];

}

\Zend_Debug::dump($positions);



