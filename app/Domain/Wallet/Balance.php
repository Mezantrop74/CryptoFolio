<?php

namespace App\Domain\Wallet;

use App\Models\Rate;
use App\Models\Wallet;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Brick\Money\Money;

/**
 * Class Balance
 * @package App\Domain\Wallet
 */
class Balance
{
    /**
     * @param Wallet $wallet
     * @param Rate|null $rate
     * @return array
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     */
    public static function usd(Wallet $wallet, Rate $rate = null)
    {
        if (!isset($rate)) {
            $rate = Rate::where('crypto_id', $wallet->crypto_id)->latest()->limit(1)->first();
        }
        $rate = BigDecimal::of($rate->rate ?? 0);

        return [
            'rate' => $rate,
            'usd' => Money::of(BigDecimal::of($wallet->balance->getAmount())
                ->multipliedBy($rate), 'USD', null, RoundingMode::DOWN)
        ];
    }
}
