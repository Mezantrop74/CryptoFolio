<?php

namespace App\Console\Commands;

use App\Domain\CoinMarketCap\Api as CMCApi;
use App\Domain\Ticker\Tickers;
use App\Models\Crypto;
use Exception;
use Illuminate\Console\Command;
use Log;

class RefreshCrypto extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crypto:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh info (name, symbol...) about all active cryptos';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $notListedCryptos = Crypto::whereNull('cmc_id')->where('ticker_type', '=', Tickers::CUSTOM)->get()->keyBy('symbol');
            $notListedCryptoKeys = array_map('mb_strtoupper', array_keys($notListedCryptos->toArray()));
            $resp = (new CMCApi)->_call('/v1/cryptocurrency/map', ['listing_status' => 'active']);
            $chunks = array_chunk($resp['data'], 1000);
            foreach ($chunks as $chunk) {
                $cryptos = [];
                foreach ($chunk as $c) {
                    try {
                        if (in_array(mb_strtoupper($c['symbol']), $notListedCryptoKeys)) {
                            if ($nowListedCrypto = $notListedCryptos[mb_strtoupper($c['symbol'])]) {
                                $nowListedCrypto->update(
                                    [
                                        'cmc_id' => $c['id'],
                                        'name' => $c['name'],
                                        'symbol' => $c['symbol'],
                                        'slug' => $c['slug'],
                                        'platform' => $c['platform'] ? json_encode($c['platform']) : null,
                                        'ticker_type' => Tickers::CMC,
                                    ]
                                );
                            }
                            continue;
                        }
                    } catch (Exception $e) {
                        Log::channel('cmdlog')->error(sprintf('%s: %s in %s:%s', get_class($e), $e->getMessage(), $e->getFile(), $e->getLine()));
                    }
                    $cryptos[] = [
                        'cmc_id' => $c['id'],
                        'name' => $c['name'],
                        'symbol' => $c['symbol'],
                        'slug' => $c['slug'],
                        'platform' => $c['platform'] ? json_encode($c['platform']) : null,
                    ];
                }
                Crypto::upsert($cryptos, ['cmc_id'], ['name', 'symbol', 'slug', 'platform']);

            }

            echo count($resp['data']);
        } catch (Exception $e) {
            Log::channel('cmdlog')->error(sprintf('%s: %s in %s:%s', get_class($e), $e->getMessage(), $e->getFile(), $e->getLine()));
        }

        return 0;
    }
}
