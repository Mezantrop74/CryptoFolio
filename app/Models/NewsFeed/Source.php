<?php

namespace App\Models\NewsFeed;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Source extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'newsfeed_sources';

    protected $fillable = ['source_id', 'name', 'link', 'creator_id', 'source_type', 'last_post_at'];
    protected $casts = [
        'last_post_at' => 'datetime'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'source_id');
    }

}
