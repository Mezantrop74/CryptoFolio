<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWalletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->uuid('wallet_id')->unique();
            $table->string('name');
            $table->foreignIdFor(\App\Models\User::class);
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreignIdFor(\App\Models\Crypto::class);
            $table->foreign('crypto_id')->references('id')->on('cryptos');
            $table->decimal('balance', 28, 14)->default(0);
            $table->text('note')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('wallets');
    }
}
