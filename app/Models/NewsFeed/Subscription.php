<?php

namespace App\Models\NewsFeed;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $table = 'newsfeed_subscriptions';
    protected $fillable = ['user_id', 'subscription_id', 'source_id', 'with_notify'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function source()
    {
        return $this->belongsTo(Source::class, 'source_id');
    }
}
