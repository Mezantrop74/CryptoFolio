<?php

namespace App\Http\Controllers\NewsFeed;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function delete(Request $request)
    {
        $this->validate($request, [
           'subscription_id' => 'required|uuid'
        ]);
        $subscription = auth()->user()->newsFeedSubscriptions()->where('subscription_id', $request->subscription_id)->firstOrFail();
        $subscription->delete();
        return redirect()->back()->with('success', __("You have successfully unsubscribed from this data source."));
    }

    public function toggleNotifications(Request $request)
    {
        $this->validate($request, [
            'subscription_id' => 'required|uuid'
        ]);
        $subscription = auth()->user()->newsFeedSubscriptions()->where('subscription_id', $request->subscription_id)->firstOrFail();
        $subscription->with_notify = !$subscription->with_notify;
        $subscription->save();
        return redirect()->back()->with('success', __($subscription->with_notify ?
            "Notifications from this data source were successfully enabled."
            : "Notifications from this data source were successfully disabled."));
    }
}
