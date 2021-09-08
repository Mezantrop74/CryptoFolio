<?php


namespace App\Domain\Jabber;


use App\Domain\Convertor\Str;
use App\Models\CryptoNotification;
use App\Models\Rate;
use Carbon\Carbon;

/**
 * Class Notifier
 * @package App\Domain\Jabber
 */
class Notifier
{
    /**
     *
     */
    const TIMEOUT = 30;

    /**
     * @return array
     */
    public static function RateNotifications()
    {
        $rates = [];
        $messages = [];
        $triggeringNotifications = [];
        $notifications = CryptoNotification::with('user', 'crypto', 'observer')->orderByRaw('SIGN(trigger_percent) DESC, ABS(trigger_percent) DESC')->get();
        foreach ($notifications as $n) {
            if (isset($triggeringNotifications[$n->user->jabber][$n->crypto_id])) {
                continue;
            }
            if ($n->last_notified_at != null && Carbon::now()->diffInMinutes($n->last_notified_at) < self::TIMEOUT) {
                continue;
            }
            if (!in_array($n->crypto_id, array_keys($rates))) {
                $r = Rate::select('rate')
                    ->whereRaw("created_at >= CURDATE() AND created_at < CURDATE() + INTERVAL 1 DAY")
                    ->where('crypto_id', $n->crypto_id)->orderBy('created_at', 'asc')->limit(1)
                    ->unionAll(Rate::select('rate')
                        ->whereRaw("created_at >= CURDATE() AND created_at < CURDATE() + INTERVAL 1 DAY")
                        ->where('crypto_id', $n->crypto_id)->orderBy('created_at', 'desc')->limit(1))
                    ->pluck('rate')->toArray();
                if (count($r) == 0) {
                    continue;
                }
                $rates[$n->crypto_id] = [
                    'start' => $r[0],
                    'current' => $r[1],
                    'diff' => round(($r[1] / $r[0]) * 100, 2) - 100
                ];
            }

            if ($n->trigger_percent < 0) {
                $isTriggering = $n->trigger_percent > $rates[$n->crypto_id]['diff'];
            } else {
                $isTriggering = $n->trigger_percent < $rates[$n->crypto_id]['diff'];
            }

            if ($isTriggering) {
                if (!isset($n->user->jabber)) {
                    continue;
                }

                if ($n->mute_empty) {
                    $observerBalance = $n->observer->wallets()->selectRaw('SUM(balance) as full_balance')->first();
                    if ($observerBalance->full_balance == 0) {
                        continue;
                    }
                }

                $n->update(['last_notified_at' => Carbon::now()]);

                $message = sprintf("\n%s (%s) %s%%\n%s: %s $\n%s: %s $\n",
                    $n->crypto->name,
                    $n->crypto->symbol,
                    $rates[$n->crypto_id]['diff'],
                    __('Start'),
                    Str::TrimZeroes($rates[$n->crypto_id]['start']),
                    __('Current'),
                    Str::trimZeroes($rates[$n->crypto_id]['current']));

                $triggeringNotifications[$n->user->jabber][$n->crypto_id] = $message;
            }
        }

        foreach ($triggeringNotifications as $jabber => $notification) {
            $messages[$jabber] = implode("\n", $notification);
        }

        return $messages;
    }
}
