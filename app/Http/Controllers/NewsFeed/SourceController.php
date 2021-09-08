<?php

namespace App\Http\Controllers\NewsFeed;

use App\Http\Controllers\Controller;
use App\Models\NewsFeed\Source;
use App\Models\NewsFeed\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SourceController extends Controller
{
    public function store(Request $request)
    {
        $this->validate($request, [
            'link' => 'required|string|min:5|max:300|regex:/^(?=.*[A-Za-z0-9]$)[A-Za-z][A-Za-z\d_]{0,64}$/m',
            'with_notify' => 'nullable'
        ]);

        $source = Source::select('id')->where('link', $request->link)->first();
        if (!isset($source->id)) {
            $source = new Source([
                'source_id' => Str::uuid(),
                'link' => Str::lower($request->link),
                'name' => Str::ucfirst(Str::lower($request->link)),
                'creator_id' => auth()->user()->id,
                'source_type' => array_keys(config('newsfeed.source_types'))[0],
            ]);
            $source->save();
        }

        $isAlreadySubscribed = auth()->user()->newsFeedSubscriptions()->whereHas('source', function ($q) use ($request) {
            $q->where('link', $request->link);
        })->exists();

        if ($isAlreadySubscribed) {
            return redirect()->back()->with('error', __('You have already subscribed to this data source.'));
        }

        (new Subscription([
            'user_id' => auth()->user()->id,
            'subscription_id' => Str::uuid(),
            'source_id' => $source->id,
            'with_notify' => isset($request->with_notify),
        ]))->save();

        return redirect()->back()->with('success', __('Source was successfully added to your news feed.'));
    }
}
