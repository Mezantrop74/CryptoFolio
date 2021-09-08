<?php

namespace App\Domain\CoinMarketCap;


use App\Domain\Ticker\Tickers;
use App\Models\Crypto;
use App\Models\Rate;
use Carbon\Carbon;
use Log;

/**
 * Class RateFiller
 * @package App\Domain\CoinMarketCap
 */
class RateFiller
{
    /**
     *
     */
    const CMC_ITEMS_LIMIT = 9000;

    /**
     * @throws \Exception
     */
    public static function fill()
    {
        try {
            $lastRate = Rate::selectRaw('MAX(created_at) as created_at')->whereHas('crypto', function ($q) {
                $q->where('ticker_type', Tickers::CMC);
            })->latest()->first();
            $startTime = isset($lastRate) ? $lastRate->created_at : Carbon::now()->subDays(90);
            $endTime = Carbon::now();
            $crypto = Crypto::select('id', 'cmc_id')
                ->distinct()
                ->whereHas('observers')
                ->whereNotNull('cmc_id');
            $weekDiff = Carbon::now()->diffInWeeks($startTime);
            $interval = $weekDiff > 1 ? '24h' : '30m';
            $crypto
                ->chunk(3, function ($cryptos) use ($lastRate, $startTime, $endTime, $interval) {
                    try {
                        $cmcIds = [];
                        foreach ($cryptos as $crypto) {
                            $cmcIds[$crypto->cmc_id] = $crypto->id;
                        }
                        $resp = (new Api)->_call('/v1/cryptocurrency/quotes/historical', [
                            'id' => implode(",", array_keys($cmcIds)),
                            'time_start' => $startTime->toIso8601String(),
                            'time_end' => $endTime->toIso8601String(),
                            'count' => 10000,
                            'interval' => $interval,
                            'skip_invalid' => 'true',
                            'aux' => 'price',
                        ]);
                        if (isset($resp['data']) && $cryptos->count() == 1) {
                            $id = $cmcIds[$resp['data']['id']];
                            $chunks = array_chunk($resp['data']['quotes'], 10);
                            foreach ($chunks as $chunk) {
                                $items = [];
                                foreach ($chunk as $quote) {
                                    $items[] = [
                                        'crypto_id' => $id,
                                        'rate' => $quote['quote']['USD']['price'],
                                        'ticker_type' => Tickers::CMC,
                                        'created_at' => Carbon::parse($quote['timestamp'])->setTimezone(config('app.timezone'))->toDateTimeString()
                                    ];
                                }
                                Rate::insert($items);
                            }
                        }

                        if (isset($resp['data']) && $cryptos->count() > 1) {
                            foreach ($resp['data'] as $cmc_id => $cryptoQuote) {
                                $id = $cmcIds[$cmc_id];
                                $chunks = array_chunk($cryptoQuote['quotes'], 20);
                                foreach ($chunks as $chunk) {
                                    $items = [];
                                    foreach ($chunk as $quote) {
                                        $items[] = [
                                            'crypto_id' => $id,
                                            'rate' => $quote['quote']['USD']['price'],
                                            'ticker_type' => Tickers::CMC,
                                            'created_at' => Carbon::parse($quote['timestamp'])->setTimezone(config('app.timezone'))->toDateTimeString()
                                        ];
                                    }
                                    Rate::insert($items);
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        Log::channel('cmdlog')->error(sprintf('%s: %s in %s:%s', get_class($e), $e->getMessage(), $e->getFile(), $e->getLine()));
                    } finally {
                        sleep(1);
                    }
                });
        } catch (\Exception $e) {
            Log::channel('cmdlog')->error(sprintf('%s: %s in %s:%s', get_class($e), $e->getMessage(), $e->getFile(), $e->getLine()));
            throw $e;
        }
    }
}

