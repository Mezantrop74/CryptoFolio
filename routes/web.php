<?php

use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Admin\RateController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\CryptoController;
use App\Http\Controllers\CryptoNotificationController;
use App\Http\Controllers\CryptoObserverController;
use App\Http\Controllers\ExchangeController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\JabberController;
use App\Http\Controllers\NewsFeed\SourceController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes([
    'verify' => false,
    'register' => false,
    'reset' => false,
]);

Route::middleware(['auth', 'user_active', 'lang'])->group(function () {
    Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::prefix('settings')->group(function () {
        Route::get('', [SettingsController::class, 'index'])->name('settings.index');
        Route::prefix('update')->group(function () {
            Route::post('user', [SettingsController::class, 'updateUser'])->name('settings.user.update');
            Route::post('view', [SettingsController::class, 'updateView'])->name('settings.view.update');
            Route::post('colorscheme', [SettingsController::class, 'toggleColorScheme'])->name('settings.view.colorscheme.toggle');
        });
    });

    Route::prefix('crypto')->group(function () {
        Route::get('', [CryptoController::class, 'index'])->name('crypto.index');
        Route::prefix('observer')->group(function () {
            Route::get('{observer_id}', [CryptoObserverController::class, 'show'])->name('crypto.observer.show');
            Route::post('create', [CryptoObserverController::class, 'create'])->name('crypto.observer.create');
            Route::post('create/custom', [CryptoObserverController::class, 'createCustom'])->name('crypto.observer.custom');
            Route::post('delete', [CryptoObserverController::class, 'delete'])->name('crypto.observer.delete');
        });

        Route::prefix('rates')->group(function () {
            Route::post('create', [\App\Http\Controllers\RateController::class, 'create'])->name('crypto.rates.create');
        });

        Route::prefix('wallets')->group(function () {
            Route::post('create/watch-only', [WalletController::class, 'createWatchOnly'])->name('crypto.wallet.create.watch.only');
            Route::post('create', [WalletController::class, 'create'])->name('crypto.wallet.create');
            Route::post('delete', [WalletController::class, 'delete'])->name('crypto.wallet.delete');
        });

        Route::prefix('exchange')->group(function () {
            Route::get('', [ExchangeController::class, 'index'])->name('crypto.exchange.index');
            Route::post('create', [ExchangeController::class, 'create'])->name('crypto.exchange.create');
            Route::post('update', [ExchangeController::class, 'update'])->name('crypto.exchange.update');
            Route::post('delete', [ExchangeController::class, 'delete'])->name('crypto.exchange.delete');
            Route::get('{exchange_id}', [ExchangeController::class, 'show'])->name('crypto.exchange.show');
        });

        Route::prefix('notification')->group(function () {
            Route::post('create', [CryptoNotificationController::class, 'create'])->name('crypto.notification.create');
            Route::post('delete', [CryptoNotificationController::class, 'delete'])->name('crypto.notification.delete');
        });

        Route::prefix('jabber')->group(function () {
            Route::post('update', [JabberController::class, 'update'])->name('jabber.update');
        });

    });

    Route::prefix('reports')->group(function () {
        Route::get('', [ReportsController::class, 'index'])->name('reports.index');
        Route::post('create', [ReportsController::class, 'create'])->name('reports.create');
    });

    Route::prefix('news-feed')->group(function () {
        Route::get('', [App\Http\Controllers\NewsFeed\FeedController::class, 'index'])->name('newsfeed.index');
        Route::prefix('sources')->group(function () {
           Route::post('store', [SourceController::class, 'store'])->name('newsfeed.source.store');
        });
        Route::prefix('subscriptions')->group(function() {
           Route::post('unsubscribe', [\App\Http\Controllers\NewsFeed\SubscriptionController::class, 'delete'])->name('newsfeed.subscription.delete');
           Route::post('toggle-notifications', [\App\Http\Controllers\NewsFeed\SubscriptionController::class, 'toggleNotifications'])->name('newsfeed.subscription.notifications.toggle');
        });
    });

    // Admin pages block
    Route::middleware(['user_admin'])->prefix('admin-zone')->group(function () {
        Route::get('bip-converter', [\App\Http\Controllers\BipController::class, 'index'])->name('bip-converter.index');

        Route::prefix('users')->group(function () {
            Route::get('', [UserController::class, 'index'])->name('admin.users.index');
            Route::post('update', [UserController::class, 'update'])->name('admin.users.update');
            Route::post('update/password', [UserController::class, 'password'])->name('admin.users.update.password');
            Route::get('create', [UserController::class, 'indexCreate'])->name('admin.users.create.index');
            Route::post('create', [UserController::class, 'create'])->name('admin.users.create');
            Route::get('{user_id}', [UserController::class, 'show'])->name('admin.users.show');
        });
        Route::prefix('settings')->group(function () {
            Route::get('', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('admin.settings.index');
            Route::post('update-api', [\App\Http\Controllers\Admin\SettingsController::class, 'updateApi'])->name('admin.settings.update.api');
        });

        Route::prefix('backup')->group(function () {
            Route::post('export', [BackupController::class, 'export'])->name('admin.backup.export');
            Route::post('import', [BackupController::class, 'import'])->name('admin.backup.import');
        });

        Route::prefix('rates')->group(function () {
            Route::post('fill', [RateController::class, 'fill'])->name('admin.rates.fill');
        });
    });
});
