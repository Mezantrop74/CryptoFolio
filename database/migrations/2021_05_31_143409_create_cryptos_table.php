<?php

use App\Domain\Ticker\Tickers;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCryptosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cryptos', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('cmc_id')->unique();
            $table->string('name');
            $table->string('symbol');
            $table->string('slug');
            $table->boolean('watch_rate')->default(false);
            $table->unsignedTinyInteger('ticker_type')->default(Tickers::default());
            $table->json('platform')->nullable();
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
        Schema::dropIfExists('cryptos');
    }
}
