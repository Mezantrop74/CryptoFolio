<?php

namespace App\Events;

use App\Models\Wallet;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Class WalletBalanceChanged
 * @package App\Events
 */
class WalletBalanceChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Wallet
     */
    public Wallet $wallet;

    /**
     * Create a new event instance.
     *
     * @param Wallet $wallet
     */
    public function __construct(Wallet $wallet)
    {
        $this->wallet = $wallet;
    }
}
