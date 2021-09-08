<?php


namespace App\Domain\Exchange;


use App\Models\Exchange;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;

/**
 * Class Commission
 * @package App\Domain\Exchange
 */
class Commission
{
    /**
     * @param Exchange $exchange
     * @return BigDecimal|\Brick\Math\BigNumber
     */
    public static function getCommissionCryptoAmount(Exchange $exchange)
    {
        return BigDecimal::of($exchange->sender_amount)->multipliedBy(BigDecimal::of($exchange->commission)->dividedBy(100, 8, RoundingMode::DOWN));
    }

    /**
     * @param Exchange $exchange
     * @return BigDecimal|\Brick\Math\BigNumber
     */
    public static function getCommissionUsdAmount(Exchange $exchange)
    {
        return BigDecimal::of($exchange->sender_amount)->multipliedBy($exchange->senderRate->rate)->multipliedBy(BigDecimal::of($exchange->commission)->dividedBy(100, 8, RoundingMode::DOWN))->toScale(2, RoundingMode::DOWN);
    }
}
