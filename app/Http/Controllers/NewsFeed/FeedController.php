<?php

namespace App\Http\Controllers\NewsFeed;

use App\Http\Controllers\Controller;
use App\Models\NewsFeed\Post;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    public function index()
    {
        $subscriptions = auth()->user()->newsFeedSubscriptions()->with('source')->get();
        $posts = Post::whereHas('source', function($q) {
            $q->whereHas('subscriptions', function($q) {
                $q->where('user_id', auth()->user()->id);
            });
        })->with(['source'])->orderByDesc('posted_at')->paginate(21);
        return view('newsfeed.index', compact('subscriptions', 'posts'));
    }
}
