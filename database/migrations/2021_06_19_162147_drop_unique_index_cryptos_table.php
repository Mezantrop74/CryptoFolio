<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropUniqueIndexCryptosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cryptos', function (Blueprint $table) {
            $table->dropIndex('cryptos_cmc_id_unique');
            $table->unsignedInteger('cmc_id')->nullable()->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cryptos', function (Blueprint $table) {
            //
        });
    }
}
