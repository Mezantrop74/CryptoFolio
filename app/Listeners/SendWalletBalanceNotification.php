<?php

namespace App\Listeners;

use App\Domain\Convertor\Str;
use App\Events\WalletBalanceChanged;
use Enqueue\Redis\RedisConnectionFactory;
use Illuminate\Support\Facades\Log;

/**
 * Class SendWalletBalanceNotification
 * @package App\Listeners
 */
class SendWalletBalanceNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param WalletBalanceChanged $event
     * @return void
     * @throws \Interop\Queue\Exception
     * @throws \Interop\Queue\Exception\InvalidDestinationException
     * @throws \Interop\Queue\Exception\InvalidMessageException
     */
    public function handle(WalletBalanceChanged $event)
    {
        if ($event->wallet->watch_only == false) {
            return;
        }

        $factory = new RedisConnectionFactory();
        $context = $factory->createContext();
        $queue = $context->createQueue('jabber');

        $previousBalance = $event->wallet->getOriginal('balance');
        $profit = $event->wallet->balance->minus($previousBalance);
        if($profit->isZero()) {
            return;
        }
        $message = $context->createMessage(sprintf(__("Wallet %s balance changed.\nCurrent balance: %s (%s%s) %s"),
            $event->wallet->name,
            Str::TrimZeroes($event->wallet->balance->getAmount()),
            $profit->isPositive() ? '+' : '',
            Str::TrimZeroes($profit->getAmount()),
            $event->wallet->balance->getCurrency()->getCurrencyCode(),
        ), ['jabber' => $event->wallet->user->jabber]);
        $context->createProducer()->send($queue, $message);
    }
}
