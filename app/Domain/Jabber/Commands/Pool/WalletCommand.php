<?php


namespace App\Domain\Jabber\Commands\Pool;


use App\Domain\Convertor\Str;
use App\Domain\Jabber\Commands\Command;
use App\Domain\Jabber\Commands\ICommand;
use App\Domain\Jabber\Message;
use App\Domain\Wallet\Balance;
use App\Models\User;
use Exception;
use Log;

/**
 * Class WalletCommand
 * @package App\Domain\Jabber\Commands\Pool
 */
class WalletCommand extends Command implements ICommand
{
    /**
     * @var string
     */
    protected static string $description = 'Sending info about wallet (balance, USD balance, rate). 1 argument Wallet name. Returns list if wallet name not unique.';

    /**
     * WalletCommand constructor.
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
            $messages = [];
            if (!isset($this->message->args()[0])) {
                return sprintf(__('Wrong argument.'));
            }

            $wallets = $this->user->wallets()->where('name', implode(' ', $this->message->args()))->with('crypto')->get();
            if ($wallets->count() == 0) {
                return sprintf(__('Wrong argument.'));
            }
            foreach ($wallets as $wallet) {
                $balance = Balance::usd($wallet);
                $messages[] = sprintf(__("%s wallet (%s)\nBalance: %s %s\nUSD Balance: %s USD\nRate: %s USD"),
                    $wallet->name,
                    $wallet->crypto->symbol,
                    Str::TrimZeroes($wallet->balance->getAmount()),
                    $wallet->crypto->symbol,
                    $balance['usd']->formatWith(Str::formatter()),
                    Str::TrimZeroes($balance['rate']->toFloat()),
                );
            }
            if (count($messages) == 1) {
                return $messages[0];
            } else {
                return implode("\n======\n", $messages);
            }
        } catch (Exception $e) {
            $message = __('Something went wrong. Contact support if problem persists.');
            Log::channel('cmdlog')->error(sprintf('%s: %s in %s:%s', get_class($e), $e->getMessage(), $e->getFile(), $e->getLine()));
        }
        return $message;
    }
}
