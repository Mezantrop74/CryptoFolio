<?php


namespace App\Domain\CryptoObserver\Api;


use App\Domain\Convertor\Str;
use App\Models\CryptoObserver;
use App\Models\Rate;
use App\Models\User;
use Brick\Math\RoundingMode;
use Brick\Money\CurrencyConverter;
use Brick\Money\Exception\CurrencyConversionException;
use Brick\Money\Exception\UnknownCurrencyException;
use Brick\Money\ExchangeRateProvider\ConfigurableProvider;
use Brick\Money\Money;
use Brick\Money\MoneyBag;

class Stats
{
    /**
     * @param CryptoObserver $observer
     * @param string $period
     * @return array
     * @throws CurrencyConversionException
     */
    public function observerStats(CryptoObserver $observer, $period = '24h')
    {
        $cryptoBag = new MoneyBag();
        $usdBag = new MoneyBag();
        $woCryptoBag = new MoneyBag();
        $woUsdBag = new MoneyBag();
        $periodUsdBag = new MoneyBag();
        $periodWoUsdBag = new MoneyBag();
        $result = [
            'rate' => 'Wait',
            'currency' => $observer->crypto->symbol,
            'wallets' => [],
            'wo_wallets' => [],
            'total' => [
                'crypto' => $cryptoBag,
                'usd' => $usdBag,
            ],
            'change' => [
                'percent' => '0.00',
                'positive' => true
            ]
        ];

        switch ($period) {
            case '1h':
                $rawInterval = 'created_at >= NOW() - interval 1 hour';
                break;
            case '3h':
                $rawInterval = 'created_at >= NOW() - interval 3 hour';
                break;
            case '12h':
                $rawInterval = 'created_at >= NOW() - interval 12 hour';
                break;
            case '24h':
                $rawInterval = 'created_at >= NOW() - interval 24 hour';
                break;
            case 'week':
                $rawInterval = 'created_at >= NOW() - interval 7 day';
                break;
            case 'month':
                $rawInterval = 'created_at >= NOW() - interval 1 month';
                break;
            case '3month':
                $rawInterval = 'created_at >= NOW() - interval 3 month';
                break;
            case '6month':
                $rawInterval = 'created_at >= NOW() - interval 6 month';
                break;
            case '12month':
                $rawInterval = 'created_at >= NOW() - interval 12 month';
                break;
            default:
                $rawInterval = 'created_at >= NOW() - interval 24 hour';
        }

        $rate = Rate::select('rate', 'created_at')->where('crypto_id', $observer->crypto_id)->latest()->first();
        if (isset($rate)) {
            $result['rate'] = $rate->rate;
            $periodRate = Rate::select('rate', 'created_at')->where('crypto_id', $observer->crypto_id)->whereRaw($rawInterval)->limit(1)->first();
            if (!$periodRate) {
                $periodRate = $rate;
            }
            $percent = (($rate->rate - $periodRate->rate) / $periodRate->rate) * 100;
            $result['change']['percent'] = number_format($percent, 2);
            $result['change']['positive'] = $percent >= 0;
        }


        $result['change']['usd'] = "";

        $provider = new ConfigurableProvider();
        $provider->setExchangeRate($observer->crypto->currency, 'USD', $rate->rate ?? 0);
        $converter = new CurrencyConverter($provider);

        $periodProvider = new ConfigurableProvider();
        $periodProvider->setExchangeRate($observer->crypto->currency, 'USD', $periodRate->rate ?? 0);
        $periodConverter = new CurrencyConverter($periodProvider);

        foreach ($observer->wallets as $wallet) {
            $crypto = $wallet->balance;
            $usd = $converter->convert($wallet->balance, 'USD', RoundingMode::DOWN);
            $periodUsd = $periodConverter->convert($wallet->balance, 'USD', RoundingMode::DOWN);
            $cryptoBag->add($crypto);
            $usdBag->add($usd);
            $periodUsdBag->add($periodUsd);
            $result['wallets'][$wallet->wallet_id] = [
                'name' => $wallet->name,
                'note' => $wallet->note ?? '',
                'crypto' => Str::TrimZeroes((string)$crypto->getAmount()),
                'usd' => Str::TrimZeroes((string)$usd->formatWith(Str::formatter())),
            ];
        }

        foreach ($observer->watchOnlyWallets as $wallet) {
            $crypto = $wallet->balance;
            $usd = $converter->convert($wallet->balance, 'USD', RoundingMode::DOWN);
            $periodUsd = $periodConverter->convert($wallet->balance, 'USD', RoundingMode::DOWN);
            $woCryptoBag->add($crypto);
            $woUsdBag->add($usd);
            $periodWoUsdBag->add($periodUsd);
            $result['wo_wallets'][$wallet->wallet_id] = [
                'name' => $wallet->name,
                'address' => $wallet->address,
                'note' => $wallet->note ?? '',
                'crypto' => Str::TrimZeroes((string)$crypto->getAmount()),
                'usd' => Str::TrimZeroes((string)$usd->formatWith(Str::formatter())),
            ];
        }

        $result['total'] = [
            'crypto' => $cryptoBag,
            'usd' => $usdBag,
            'wo_crypto' => $woCryptoBag,
            'wo_usd' => $woUsdBag,
        ];
        $result['change']['usd'] = (clone $usdBag)->subtract($periodUsdBag);
        $result['change']['wo_usd'] = (clone $woUsdBag)->subtract($periodWoUsdBag);
        return $result;
    }

    /**
     * @param User $user
     * @param string $period
     * @return array
     * @throws CurrencyConversionException
     * @throws UnknownCurrencyException
     */
    public function observersStats(User $user, $period = '24h')
    {
        $results = [
            'observers' => [],
            'crypto' => [
                'usd' => '0'
            ],
            'tokens' => [

            ]
        ];

        $cryptoUsdBag = new MoneyBag();
        $woCryptoUsdBag = new MoneyBag();
        $tokensUsdBag = new MoneyBag();
        $woTokensUsdBag = new MoneyBag();
        $user->cryptoObservers()
            ->with('crypto')
            ->chunk(10, function ($observers) use ($period, &$results, &$cryptoUsdBag, &$tokensUsdBag, &$woCryptoUsdBag, &$woTokensUsdBag) {
                foreach ($observers as $observer) {
                    $stats = $this->observerStats($observer, $period);
                    if ($observer->crypto->platform == null) {
                        $cryptoUsdBag = $cryptoUsdBag->add($stats['total']['usd']);
                        $woCryptoUsdBag = $woCryptoUsdBag->add($stats['total']['wo_usd']);
                    } else {
                        $tokensUsdBag = $tokensUsdBag->add($stats['total']['usd']);
                        $woTokensUsdBag = $woTokensUsdBag->add($stats['total']['wo_usd']);
                    }
                    $stats['total'] = [
                        'crypto' => Str::TrimZeroes((string)Money::of($stats['total']['crypto']->getAmount($observer->crypto->currency), $observer->crypto->currency)->getAmount()),
                        'wo_crypto' => Str::TrimZeroes((string)Money::of($stats['total']['wo_crypto']->getAmount($observer->crypto->currency), $observer->crypto->currency)->getAmount()),
                        'usd' => Str::TrimZeroes((string)Money::of($stats['total']['usd']->getAmount('USD'), 'USD')->formatWith(Str::formatter())),
                        'wo_usd' => Str::TrimZeroes((string)Money::of($stats['total']['wo_usd']->getAmount('USD'), 'USD')->formatWith(Str::formatter())),
                    ];
                    $stats['rate'] = Str::Beautify($stats['rate']);
                    $stats['change']['usd'] = Str::TrimZeroes((string)Money::of($stats['change']['usd']->getAmount('USD'), 'USD')->formatWith(Str::formatter()));
                    $stats['change']['wo_usd'] = Str::TrimZeroes((string)Money::of($stats['change']['wo_usd']->getAmount('USD'), 'USD')->formatWith(Str::formatter()));
                    $results['observers'][$observer->observer_id] = $stats;
                }
            });
        $results['crypto']['usd'] = (string)Money::of($cryptoUsdBag->getAmount('USD'), 'USD')->formatWith(Str::formatter());
        $results['crypto']['wo_usd'] = (string)Money::of($woCryptoUsdBag->getAmount('USD'), 'USD')->formatWith(Str::formatter());
        $results['tokens']['usd'] = (string)Money::of($tokensUsdBag->getAmount('USD'), 'USD')->formatWith(Str::formatter());
        $results['tokens']['wo_usd'] = (string)Money::of($woTokensUsdBag->getAmount('USD'), 'USD')->formatWith(Str::formatter());
        $results['all']['usd'] = (string)Money::of($cryptoUsdBag->add($tokensUsdBag)->getAmount('USD'), 'USD')->formatWith(Str::formatter());
        $results['all']['wo_usd'] = (string)Money::of($woCryptoUsdBag->add($woTokensUsdBag)->getAmount('USD'), 'USD')->formatWith(Str::formatter());

        return $results;
    }
}
