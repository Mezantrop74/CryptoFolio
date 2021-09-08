<?php

use App\Domain\Ticker\Tickers;
use App\Models\Crypto;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rates', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Crypto::class);
            $table->foreign('crypto_id')->references('id')->on('cryptos');
            $table->decimal('rate', 28, 14)->default(0);
            $table->unsignedTinyInteger('ticker_type')->default(Tickers::default());
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
        Schema::dropIfExists('rates');
    }
}
