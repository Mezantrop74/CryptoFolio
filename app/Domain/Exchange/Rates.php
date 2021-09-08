<?php


namespace App\Domain\Exchange;


use App\Models\CryptoObserver;
use App\Models\Rate;
use Carbon\Carbon;
use DB;

/**
 * Class Rates
 * @package App\Domain\Exchange
 */
class Rates
{
    /**
     * @param CryptoObserver $observer
     * @param string $period
     * @param bool $withDateFormat
     * @return array
     */
    public static function historicalRates(CryptoObserver $observer, $period = '24h', bool $withDateFormat = true)
    {
        $rates = Rate::select(['rate', 'created_at'])->where([
            'crypto_id' => $observer->crypto_id,
        ]);

        switch ($period) {
            case '1h':
                $rates = Rate::selectRaw('rate,created_at')->where([
                    'crypto_id' => $observer->crypto_id,

                ])->whereIntegerInRaw('id', Rate::selectRaw('MAX(id) as id')->where([
                    'crypto_id' => $observer->crypto_id,

                ])->where('created_at', '>=', $observer->created_at)->whereRaw('created_at >= NOW() - interval 1 hour')->groupBy(DB::raw('created_at'))->pluck('id')->toArray());
                break;
            case '3h':
                $rates = Rate::selectRaw('rate,created_at')->where([
                    'crypto_id' => $observer->crypto_id,

                ])->whereIntegerInRaw('id', Rate::selectRaw('MAX(id) as id')->where([
                    'crypto_id' => $observer->crypto_id,

                ])->where('created_at', '>=', $observer->created_at)->whereRaw('created_at >= NOW() - interval 3 hour')->groupBy(DB::raw('created_at'))->pluck('id')->toArray());
                break;
            case '12h':
                $rates = Rate::selectRaw('rate,created_at')->where([
                    'crypto_id' => $observer->crypto_id,

                ])->whereIntegerInRaw('id', Rate::selectRaw('MAX(id) as id')->where([
                    'crypto_id' => $observer->crypto_id,

                ])->where('created_at', '>=', $observer->created_at)->whereRaw('created_at >= NOW() - interval 12 hour')->groupBy(DB::raw('Hour(created_at)'))->pluck('id')->toArray());
                break;
            case '24h':
                $rates = Rate::selectRaw('rate,created_at')->where([
                    'crypto_id' => $observer->crypto_id,

                ])->where('created_at', '>=', $observer->created_at)->whereIntegerInRaw('id', Rate::selectRaw('MAX(id) as id')->where([
                    'crypto_id' => $observer->crypto_id,

                ])->whereRaw('created_at >= NOW() - interval 1 day')->groupBy(DB::raw('Hour(created_at)'))->pluck('id')->toArray());
                break;
            case 'week':
                $rates = Rate::selectRaw('rate,created_at')->where([
                    'crypto_id' => $observer->crypto_id,

                ])->whereIntegerInRaw('id', Rate::selectRaw('MAX(id) as id')->where([
                    'crypto_id' => $observer->crypto_id,

                ])->where('created_at', '>=', $observer->created_at)->whereRaw('DATE(created_at) >= curdate() - interval 7 day')->groupBy(DB::raw('Day(created_at)'))->pluck('id')->toArray());
                break;
            case 'month':
                $rates = Rate::selectRaw('rate,created_at')->where([
                    'crypto_id' => $observer->crypto_id,

                ])->whereIntegerInRaw('id', Rate::selectRaw('MAX(id) as id')->where([
                    'crypto_id' => $observer->crypto_id,

                ])->where('created_at', '>=', $observer->created_at)->whereRaw('DATE(created_at) >= curdate() - interval 1 month')->groupBy(DB::raw('Day(created_at)'))->pluck('id')->toArray());
                break;
            case '3month':
                $rates = Rate::selectRaw('rate,created_at')->where([
                    'crypto_id' => $observer->crypto_id,

                ])->whereIntegerInRaw('id', Rate::selectRaw('MAX(id) as id')->where([
                    'crypto_id' => $observer->crypto_id,

                ])->where('created_at', '>=', $observer->created_at)->whereRaw('DATE(created_at) >= curdate() - interval 3 month')->groupBy(DB::raw('Week(created_at)'))->pluck('id')->toArray());
                break;
            case '6month':
                $rates = Rate::selectRaw('rate,created_at')->where([
                    'crypto_id' => $observer->crypto_id,

                ])->whereIntegerInRaw('id', Rate::selectRaw('MAX(id) as id')->where([
                    'crypto_id' => $observer->crypto_id,

                ])->where('created_at', '>=', $observer->created_at)->whereRaw('DATE(created_at) >= curdate() - interval 6 month')->groupBy(DB::raw('Week(created_at)'))->pluck('id')->toArray());
                break;
            case '12month':
                $rates = Rate::selectRaw('rate,created_at')->where([
                    'crypto_id' => $observer->crypto_id,

                ])->whereIntegerInRaw('id', Rate::selectRaw('MAX(id) as id')->where([
                    'crypto_id' => $observer->crypto_id,

                ])->where('created_at', '>=', $observer->created_at)->whereRaw('DATE(created_at) >= curdate() - interval 1 year')->groupBy(DB::raw('Month(created_at)'))->pluck('id')->toArray());
                break;
            default:
        }

        $rates = $rates->orderBy('created_at')->pluck('rate', 'created_at');
        if ($withDateFormat == true) {
            $format = self::periodDateFormat($period);
            $formattedRates = [];
            foreach ($rates as $date => $rate) {
                $formattedRates[self::nearest5Mins($date)->format($format)] = $rate;
            }
            return $formattedRates;
        }
        return $rates;
    }

    /**
     * @param $period
     * @return string
     */
    private static function periodDateFormat($period)
    {
        switch ($period) {
            case '1h':
            case '3h':
                $format = "H:i";
                break;
            case '12h':
            case '24h':
                $format = "d/m H:i";
                break;
            case 'month':
            case 'week':
                $format = 'd/m';
                break;
            case '3month':
            case '6month':
            case '12month':
                $format = 'm/Y';
                break;
            default:
                $format = 'Y-m-d H:i:s';
        }
        return $format;
    }

    /**
     * @param $time
     * @return Carbon
     */
    private static function nearest5Mins($time)
    {
        $time = (round(strtotime($time) / 300)) * 300;
        return Carbon::createFromTimestamp($time);
    }

}
