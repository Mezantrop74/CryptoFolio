<?php

namespace App\Domain\Exchange;

use Brick\Math\BigDecimal;

/**
 * Class Profit
 * @package App\Domain\Exchange
 */
class Profit
{
    /**
     * @param $currentReceiverRate
     * @param $receiverRate
     * @param bool $asString
     * @return BigDecimal|\Brick\Math\BigNumber|string
     */
    public static function Calculate($currentReceiverRate, $receiverRate, bool $asString = false)
    {
        $profit = BigDecimal::of($currentReceiverRate)->minus($receiverRate);
        return $asString ? (string)$profit : $profit;
    }
}
