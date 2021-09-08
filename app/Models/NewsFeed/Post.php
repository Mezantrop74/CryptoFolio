<?php

namespace App\Models\NewsFeed;

use App\Events\NewsFeedPostCreated;
use App\Events\WalletBalanceChanged;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'newsfeed_posts';

    protected $fillable = ['post_id', 'origin_post_id', 'source_id', 'content', 'posted_at'];
    protected $casts = [
        'posted_at' => 'datetime'
    ];

    /**
     * @var string[]
     */
    protected $dispatchesEvents = [
        'created' => NewsFeedPostCreated::class,
    ];

    public function source()
    {
        return $this->belongsTo(Source::class, 'source_id');
    }
}
