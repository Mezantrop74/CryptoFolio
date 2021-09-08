<?php

use App\Models\NewsFeed\Source;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewsSubscribersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('newsfeed_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreignIdFor(Source::class, 'source_id');
            $table->foreign('source_id')->references('id')->on('newsfeed_sources');
            $table->boolean('with_notify')->default(false);
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
        Schema::dropIfExists('newsfeed_subscriptions');
    }
}
