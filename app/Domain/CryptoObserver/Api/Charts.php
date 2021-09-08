<?php


namespace App\Domain\CryptoObserver\Api;


use App\Domain\Exchange\Rates;
use App\Models\CryptoObserver;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Brick\Money\Exception\MoneyMismatchException;
use Brick\Money\Exception\UnknownCurrencyException;
use Brick\Money\Money;

/**
 * Class Charts
 * @package App\Domain\CryptoObserver\Api
 */
class Charts
{
    /**
     * @param CryptoObserver $observer
     * @param string $period
     * @param string $walletType
     * @return array
     * @throws UnknownCurrencyException
     */
    public static function ObserverChart(CryptoObserver $observer, $period = '24h', $walletType = 'nwo')
    {
        $result = [
            'errors' => [],
            'message' => "",
            'rates' => [],
            'calc_rates' => [],
        ];

        $rates = Rates::historicalRates($observer, $period);
        switch ($walletType) {
            case 'nwo':
            default:
                $wallets = $observer->wallets();
                break;
            case 'wo':
                $wallets = $observer->wallets()->watchOnly();
                break;
            case 'all':
                $wallets = $observer->wallets()->withoutGlobalScope('not-watch-only');
        }
        $observerBalance = $wallets->selectRaw('SUM(balance) as full_balance')->first()->toArray();

        $observerMoney = Money::of($observerBalance['full_balance'] ?? 0, $observer->crypto->currency);
        foreach ($rates as $date => $rate) {
            $result['calc_rates'][$date] = $observerMoney->multipliedBy($rate, RoundingMode::DOWN);
            $result['rates'][$date] = Money::of($result['calc_rates'][$date]->getAmount(), 'USD', null, RoundingMode::DOWN)->getAmount();
        }

        return $result;
    }


    /**
     * @param $observers
     * @param string $period
     * @param string $walletType
     * @return array
     * @throws MoneyMismatchException
     * @throws UnknownCurrencyException
     */
    public static function ObserverCharts($observers, $period = '24h', $walletType = 'nwo')
    {
        $results = [];
        $balances = [];
        foreach ($observers as $observer) {
            $result = self::ObserverChart($observer, $period, $walletType);
            foreach ($result['calc_rates'] as $date => $usdBalance) {
                if (!isset($results[$date])) {
                    $results[$date] = Money::zero('USD');
                }
                $results[$date] = Money::of($results[$date]->getAmount(), 'USD', null, RoundingMode::DOWN)->plus(Money::of($usdBalance->getAmount(), 'USD', null, RoundingMode::DOWN), RoundingMode::DOWN);
            }
        }
        foreach ($results as $date => $result) {
            $balances[$date] = $result->getAmount();
        }

        return $balances;
    }

    /**
     * @param CryptoObserver $left
     * @param CryptoObserver $right
     * @param string $period
     * @return array
     */
    public static function ConvertChart(CryptoObserver $left, CryptoObserver $right, $period = '24h')
    {
        $result = [];
        $leftRates = Rates::historicalRates($left, $period);
        $rightRates = Rates::historicalRates($right, $period);
        foreach ($leftRates as $leftDate => $leftRate) {
            if (!isset($rightRates[$leftDate])) {
                continue;
            }
            $result[$leftDate] = BigDecimal::of($leftRate)->dividedBy($rightRates[$leftDate], null, RoundingMode::DOWN);
        }

        return $result;
    }
}
