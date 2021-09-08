<?php

use App\Models\Crypto;
use App\Models\CryptoObserver;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCryptoNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crypto_notifications', function (Blueprint $table) {
            $table->id();
            $table->uuid('notification_id')->unique();
            $table->foreignIdFor(Crypto::class);
            $table->foreign('crypto_id')->references('id')->on('cryptos');
            $table->foreignIdFor(CryptoObserver::class);
            $table->foreign('crypto_observer_id')->references('id')->on('crypto_observers');
            $table->foreignIdFor(User::class);
            $table->foreign('user_id')->references('id')->on('users');
            $table->tinyInteger('trigger_percent');
            $table->timestamp('last_notified_at')->nullable();
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
        Schema::dropIfExists('crypto_notifications');
    }
}
