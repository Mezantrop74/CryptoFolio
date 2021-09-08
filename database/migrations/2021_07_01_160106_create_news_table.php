<?php

use App\Models\NewsFeed\Source;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('newsfeed_posts', function (Blueprint $table) {
            $table->id();
            $table->uuid('post_id')->unique();
            $table->integer('origin_post_id')->nullable();
            $table->foreignIdFor(Source::class, 'source_id');
            $table->foreign('source_id')->references('id')->on('newsfeed_sources');
            $table->mediumText('content')->nullable();
            $table->timestamp('posted_at')->nullable();
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
        Schema::dropIfExists('newsfeed_posts');
    }
}
