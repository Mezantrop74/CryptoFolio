<?php

namespace App\Domain\Exchange\Api;

use App\Models\Exchange;
use App\Models\Rate;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;

/**
 * Class Profit
 * @package App\Domain\Exchange\Api
 */
class Profit
{
    /**
     * @param Exchange $exchange
     * @return array
     */
    public function CalculateProfit(Exchange $exchange)
    {
        $result = [
            'exchange_id' => $exchange->exchange_id,
        ];

        $senderRate = Rate::where('crypto_id', $exchange->sender_crypto_id)->latest()->first();
        $receiverRate = Rate::where('crypto_id', $exchange->receiver_crypto_id)->latest()->first();
        $receiverUsd = BigDecimal::of($exchange->receiver_amount)->multipliedBy($receiverRate->rate);
        $senderUsd = BigDecimal::of($exchange->sender_amount)->multipliedBy($senderRate->rate);
        $profit = $receiverUsd->minus($senderUsd);
        $receiverExchUsd = BigDecimal::of($exchange->receiver_amount)->multipliedBy($exchange->receiverRate->rate);
        $percent = $profit->dividedBy($receiverExchUsd, null, RoundingMode::DOWN)->multipliedBy(100)->toScale(2, RoundingMode::DOWN);

        $result['is_profit'] = $profit->isPositive();
        $result['percent'] = $percent;
        $result['amount'] = $profit->toScale(2, RoundingMode::DOWN)->toFloat();
        return $result;
    }
}
