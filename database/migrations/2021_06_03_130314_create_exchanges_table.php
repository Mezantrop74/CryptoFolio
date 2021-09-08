<?php

use App\Models\Crypto;
use App\Models\CryptoObserver;
use App\Models\Rate;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExchangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exchanges', function (Blueprint $table) {
            $table->id();
            $table->uuid('exchange_id')->unique();
            $table->foreignIdFor(User::class);
            $table->foreign('user_id')->references('id')->on('users');

            $table->foreignIdFor(Crypto::class, 'sender_crypto_id');
            $table->foreign('sender_crypto_id')->references('id')->on('cryptos');

            $table->foreignIdFor(Wallet::class, 'sender_wallet_id');
            $table->foreign('sender_wallet_id')->references('id')->on('wallets');

            $table->foreignIdFor(CryptoObserver::class, 'sender_crypto_observer_id');
            $table->foreign('sender_crypto_observer_id')->references('id')->on('crypto_observers');

            $table->foreignIdFor(Rate::class, 'sender_rate_id');
            $table->foreign('sender_rate_id')->references('id')->on('rates');

            $table->decimal('sender_amount', 28, 14);


            $table->foreignIdFor(Crypto::class, 'receiver_crypto_id');
            $table->foreign('receiver_crypto_id')->references('id')->on('cryptos');

            $table->foreignIdFor(Wallet::class, 'receiver_wallet_id');
            $table->foreign('receiver_wallet_id')->references('id')->on('wallets');

            $table->foreignIdFor(CryptoObserver::class, 'receiver_crypto_observer_id');
            $table->foreign('receiver_crypto_observer_id')->references('id')->on('crypto_observers');

            $table->foreignIdFor(Rate::class, 'receiver_rate_id');
            $table->foreign('receiver_rate_id')->references('id')->on('rates');

            $table->decimal('receiver_amount', 28, 14);

            $table->decimal('commission', 28, 14)->nullable();
            $table->text('note')->nullable();


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exchanges');
    }
}
