<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameLastNewsAtNewsfeedSourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('newsfeed_sources', function (Blueprint $table) {
            $table->renameColumn('last_news_at', 'last_post_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('newsfeed_sources', function (Blueprint $table) {
            //
        });
    }
}
