<?php

use App\Http\Controllers\Api\Admin\SettingsController;
use App\Http\Controllers\Api\AppController;
use App\Http\Controllers\Api\ChartController;
use App\Http\Controllers\Api\CryptoObserverController;
use App\Http\Controllers\Api\ExchangeController;
use App\Http\Controllers\Api\WalletController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:api', 'user_active', 'throttle:180,1'])->group(function () {
    Route::prefix('app')->group(function () {
        Route::get('time', [AppController::class, 'time'])->name('api.app.time');
    });
    Route::prefix('observer')->group(function () {
        Route::get('stats', [CryptoObserverController::class, 'observer'])->name('api.observer.stats');
        Route::get('stats/all', [CryptoObserverController::class, 'observers'])->name('api.observer.stats.all');
        Route::get('exchange/profits', [CryptoObserverController::class, 'exchangeProfits'])->name('api.observer.exchange.profits');
    });

    Route::prefix('wallets')->group(function () {
        Route::get('amount', [WalletController::class, 'checkAmount'])->name('api.wallets.amount');
        Route::post('update', [WalletController::class, 'update'])->name('api.wallets.update');
        Route::middleware(['user_admin'])->get('debank', [WalletController::class, 'debank'])->name('api.wallets.debank');
        Route::middleware(['user_admin'])->get('airdrop', [WalletController::class, 'airdrop'])->name('api.wallets.airdrop');
    });

    Route::prefix('charts')->group(function () {
        Route::get('observer/rate', [ChartController::class, 'CryptoObserverRateChart'])->name('api.charts.observer.rate');
        Route::get('observers/rate', [ChartController::class, 'CryptoObserversRateChart'])->name('api.charts.observers.rates');
        Route::get('convert', [ChartController::class, 'ConvertChart'])->name('api.charts.convert');
    });

    Route::prefix('exchanges')->group(function () {
        Route::get('calc', [ExchangeController::class, 'Calculate'])->name('api.exchanges.calculate');
        Route::get('profit', [ExchangeController::class, 'ProfitController'])->name('api.exchanges.profit');
        Route::get('profits', [ExchangeController::class, 'ProfitsController'])->name('api.exchanges.profits');
    });

    Route::middleware(['user_admin'])->group(function () {
        Route::prefix('admin-zone')->group(function () {
            Route::prefix('settings')->group(function () {
                Route::get('api-usage', [SettingsController::class, 'apiUsage'])->name('api.settings.api-usage');
            });
        });

    });
});
