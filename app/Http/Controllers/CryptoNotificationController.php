<?php

namespace App\Http\Controllers;

use App\Models\CryptoNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CryptoNotificationController extends Controller
{
    public function create(Request $request)
    {
        $this->validate($request, [
            'observer_id' => 'required|string|exists:crypto_observers,observer_id',
            'is_global' => 'nullable',
            'mute_empty' => 'nullable',
            'trigger_percent' => 'required|integer|gt:-100|lte:100',
        ]);

        if($request->is_global) {
            $observers = auth()->user()->cryptoObservers()->get();
            $notifications = [];
            foreach($observers as $observer) {
                $notifications[] = [
                    'notification_id' => Str::uuid(),
                    'crypto_id' => $observer->crypto_id,
                    'crypto_observer_id' => $observer->id,
                    'user_id' => auth()->user()->id,
                    'mute_empty' => isset($request->mute_empty),
                    'trigger_percent' => $request->trigger_percent,
                    'created_at' => Carbon::now()
                ];
            }
            $notifications = array_chunk($notifications, 20);
            foreach($notifications as $notificationChunk) {
                CryptoNotification::insert($notificationChunk);
            }
        } else {
            $observer = auth()->user()->cryptoObservers()->where('observer_id', $request->observer_id)->firstOrFail();
            $notification = new CryptoNotification([
                'notification_id' => Str::uuid(),
                'crypto_id' => $observer->crypto_id,
                'crypto_observer_id' => $observer->id,
                'user_id' => auth()->user()->id,
                'mute_empty' => isset($request->mute_empty),
                'trigger_percent' => $request->trigger_percent,
            ]);
            $notification->save();
        }


        return redirect()->back()->with('success', __('Notification was successfully created.'));
    }

    public function delete(Request $request)
    {
        $this->validate($request, [
            'notification_id' => 'required|string|exists:crypto_notifications,notification_id',
        ]);

        $notification = auth()->user()->cryptoNotifications()->where('notification_id', $request->notification_id)->firstOrFail();
        $notification->delete();
        return redirect()->back()->with('success', __('Notification was successfully deleted.'));
    }
}
