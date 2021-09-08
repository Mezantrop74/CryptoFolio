<?php

namespace App\Console\Commands;

use App\Domain\CoinMarketCap\Api as CMCApi;
use App\Domain\Ticker\Tickers;
use App\Models\Crypto;
use App\Models\Rate;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Log;

class RefreshRate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rate:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $watchedCrypto = Crypto::select('id', 'cmc_id')->distinct()->whereHas('observers')->whereNotNull('cmc_id')->pluck('id', 'cmc_id')->toArray();
        try {
            $resp = (new CMCApi)->_call('/v1/cryptocurrency/quotes/latest', ['id' => implode(',', array_keys($watchedCrypto)), 'aux' => 'date_added', 'skip_invalid' => 'true']);
            $timing = Carbon::now();
            $chunks = array_chunk($resp['data'], 10);
            foreach ($chunks as $chunk) {
                $rates = [];
                foreach ($chunk as $rate) {
                    $rates[] = [
                        'crypto_id' => $watchedCrypto[$rate['id']],
                        'rate' => $rate['quote']['USD']['price'],
                        'ticker_type' => Tickers::CMC,
                        'created_at' => $timing,
                    ];
                }
                Rate::insert($rates);
            }

            echo count($resp['data']);
        } catch (Exception $e) {
            Log::channel('cmdlog')->error(sprintf('%s: %s in %s:%s', get_class($e), $e->getMessage(), $e->getFile(), $e->getLine()));
        }

    }
}
