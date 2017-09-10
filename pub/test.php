<?php

$start = $purchaseAmount = 200;
$conversionRate = 0.0399;
$feeStructure = [
    'lastFee' => null,
    'fees' => []
];

while ($purchaseAmount <= 1000) {
    $fee = round(($purchaseAmount * $conversionRate), 2, PHP_ROUND_HALF_UP);
    if ($feeStructure['lastFee'] == null) {
        $feeStructure['lastFee'] = $fee;
    }
    if ($feeStructure['lastFee'] < $fee) {
        $end = round(($purchaseAmount-.01), 2, PHP_ROUND_HALF_UP);
        $feeStructure['lastFee'] = $fee;
        $feeStructure['fees'][] = [
            'min' => $start,
            'max' => $end,
            'fee' => $fee,
            'min%' => ($fee/$end),
            'max%' => ($fee/$start),
            'spread' => round($end-$start, 2, PHP_ROUND_HALF_UP)
        ];
        $start = $purchaseAmount;
    }
    $purchaseAmount = round(($purchaseAmount+floatval(.01)),2);
}
?>
<table cellpadding="10">
<thead>
<tr>
    <th>Fee</th>
    <th>Min Bid</th>
    <th>Max Bid</th>
</tr>
</thead>
<tbody>
<?php foreach ($feeStructure['fees'] as $fee): ?>
<tr>
    <td>
        Fee: $<?php echo $fee['fee'] ?><br/>
        Spread: $<?php echo $fee['spread'] ?>

    </td>

    <td>
        $<?php echo $fee['min'] ?><br/>
        %<?php echo $fee['max%'] ?>
    </td>
    <td>
        $<?php echo $fee['max'] ?><br/>
        %<?php echo $fee['min%'] ?>
    </td>
</tr>
<?php endforeach ?>
</tbody>
</table>