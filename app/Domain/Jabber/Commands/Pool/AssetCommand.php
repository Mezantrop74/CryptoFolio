<?php


namespace App\Domain\Jabber\Commands\Pool;


use App\Domain\Convertor\Str;
use App\Domain\CryptoObserver\Api\Stats;
use App\Domain\Jabber\Commands\Command;
use App\Domain\Jabber\Commands\ICommand;
use App\Domain\Jabber\Message;
use App\Models\User;
use Brick\Money\Money;
use Exception;
use Log;

/**
 * Class AssetCommand
 * @package App\Domain\Jabber\Commands\Pool
 */
class AssetCommand extends Command implements ICommand
{
    /**
     * @var string
     */
    protected static string $description = 'Sending info about asset (balance, USD balance, rate, 24h change). 1 argument Ticker (ETH, BTC etc.).';

    /**
     * AssetCommand constructor.
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
            if (!isset($this->message->args()[0])) {
                return sprintf(__('Wrong argument.'));
            }

            $observer = $this->user->cryptoObservers()->whereHas('crypto', function ($q) {
                $q->where('symbol', $this->message->args()[0]);
            })->with(['crypto', 'wallets'])->limit(1)->get()->first();
            if (!isset($observer)) {
                return sprintf(__('Wrong argument.'));
            }
            $stats = (new Stats())->observerStats($observer);
            $stats['total'] = [
                'crypto' => Str::TrimZeroes((string)Money::of($stats['total']['crypto']->getAmount($observer->crypto->currency), $observer->crypto->currency)->getAmount()),
                'usd' => Str::TrimZeroes((string)Money::of($stats['total']['usd']->getAmount('USD'), 'USD')->formatWith(Str::formatter())),
            ];
            $stats['rate'] = Str::Beautify($stats['rate']);
            $stats['change']['usd'] = Str::TrimZeroes((string)Money::of($stats['change']['usd']->getAmount('USD'), 'USD')->formatWith(Str::formatter()));

            $message = sprintf(__("%s stats (%s)\nBalance: %s %s\nUSD Balance: %s USD\nRate: %s USD\nChange: %s USD (%s%%)"),
                $observer->crypto->name,
                __('24h'),
                $stats['total']['crypto'],
                $observer->crypto->symbol,
                $stats['total']['usd'],
                $stats['rate'],
                $stats['change']['usd'],
                $stats['change']['percent']);
        } catch (Exception $e) {
            $message = __('Something went wrong. Contact support if problem persists.');
            Log::channel('cmdlog')->error(sprintf('%s: %s in %s:%s', get_class($e), $e->getMessage(), $e->getFile(), $e->getLine()));
        }
        return $message;
    }
}
