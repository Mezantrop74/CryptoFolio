<?php


namespace App\Domain\Jabber\Commands\Pool;


use App\Domain\Convertor\Str;
use App\Domain\Jabber\Commands\Command;
use App\Domain\Jabber\Commands\ICommand;
use App\Domain\Jabber\Message;
use App\Models\Crypto;
use App\Models\Rate;
use App\Models\User;
use Exception;

/**
 * Class RateCommand
 * @package App\Domain\Jabber\Commands\Pool
 */
class RateCommand extends Command implements ICommand
{
    /**
     * @var string
     */
    protected static string $description = 'Sending info about current crypto USD rate. 1 argument Ticker (ETH, BTC etc.).';

    /**
     * RateCommand constructor.
     * @param Message $message
     * @param User $user
     */
    public function __construct(Message $message, User $user)
    {
        parent::__construct($message, $user);
    }

    /**
     * @return string
     */
    public function response(): string
    {
        try {
            $rate = Rate::where('crypto_id', Crypto::select('id')->where('symbol', $this->message->args()[0])->pluck('id')->toArray()[0])->latest()->limit(1)->first();
            $message = sprintf("%s: %s USD", $this->message->args()[0], Str::TrimZeroes($rate->rate));
        } catch (Exception $e) {
            $message = __('No rates.');
        }
        return $message;
    }
}
