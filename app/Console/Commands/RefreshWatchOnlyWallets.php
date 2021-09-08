<?php

namespace App\Console\Commands;

use App\Domain\Cryptoapis\Api;
use App\Models\Wallet;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RefreshWatchOnlyWallets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wallets:refresh';

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
        $watchOnlyWallets = Wallet::withoutGlobalScope('not-watch-only')->where('watch_only', true)->with('crypto')->get();
        $watchOnlySlugs = config('crypto.watch_only_slugs');
        $watchOnlyCmcIds = array_keys($watchOnlySlugs);
        foreach ($watchOnlyWallets as $wallet) {
            try {
                if (!in_array($wallet->crypto->cmc_id, $watchOnlyCmcIds)) {
                    continue;
                }
                $balance = (new Api)->getBalance($wallet->address, $watchOnlySlugs[$wallet->crypto->cmc_id]);
                if (!is_numeric($balance)) {
                    continue;
                }
                $wallet->balance = BigDecimal::of($balance)->toScale(14, RoundingMode::DOWN)->toFloat();
                $wallet->save();
            } catch (Exception $e) {
                Log::channel('cmdlog')->error(sprintf('%s: %s in %s:%s', get_class($e), $e->getMessage(), $e->getFile(), $e->getLine()));
            }
            sleep(1);
        }
    }
}
