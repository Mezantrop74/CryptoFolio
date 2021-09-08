<?php

use App\Models\Crypto;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCryptoObserversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crypto_observers', function (Blueprint $table) {
            $table->id();
            $table->uuid('observer_id')->unique();
            $table->foreignIdFor(User::class);
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreignIdFor(Crypto::class);
            $table->foreign('crypto_id')->references('id')->on('cryptos');
            $table->tinyInteger('ticker_type');
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
        Schema::dropIfExists('crypto_observers');
    }
}
