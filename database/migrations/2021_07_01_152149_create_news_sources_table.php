<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewsSourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('newsfeed_sources', function (Blueprint $table) {
            $table->id();
            $table->uuid('source_id')->unique();
            $table->string('name');
            $table->string('link');
            $table->foreignIdFor(User::class, 'creator_id');
            $table->foreign('creator_id')->references('id')->on('users');
            $table->integer('source_type');
            $table->timestamp('last_news_at')->nullable();
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
        Schema::dropIfExists('newsfeed_sources');
    }
}
