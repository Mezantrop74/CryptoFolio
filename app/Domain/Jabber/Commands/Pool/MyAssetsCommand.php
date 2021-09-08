<?php


namespace App\Domain\Jabber\Commands\Pool;


use App\Domain\CryptoObserver\Api\Stats;
use App\Domain\Jabber\Commands\Command;
use App\Domain\Jabber\Commands\ICommand;
use App\Domain\Jabber\Message;
use App\Models\User;
use Exception;

/**
 * Class MyAssetsCommand
 * @package App\Domain\Jabber\Commands\Pool
 */
class MyAssetsCommand extends Command implements ICommand
{
    /**
     * @var string
     */
    protected static string $description = 'Sending info about your cryptocurrency portfolio (balance, USD balance). 1 argument (all | crypto | tokens).';

    /**
     * @var array|string[]
     */
    private array $allowedArgs = ['all', 'crypto', 'tokens'];

    /**
     * MyAssetsCommand constructor.
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
            if (!isset($this->message->args()[0]) || !in_array($this->message->args()[0], $this->allowedArgs)) {
                return sprintf(__('Wrong argument. Allowed: %s'), implode(',', $this->allowedArgs));
            }

            $stats = (new Stats())->observersStats($this->user);
            switch ($this->message->args()[0]) {
                case 'all':
                    $message = sprintf(__('Your total balance: %s USD'), $stats['all']['usd']);
                    break;
                case 'crypto':
                    $message = sprintf(__('Your crypto balance: %s USD'), $stats['crypto']['usd']);
                    break;
                case 'tokens':
                    $message = sprintf(__('Your tokens balance: %s USD'), $stats['tokens']['usd']);
                    break;
                default:
                    $message = sprintf(__('Wrong argument. Allowed: %s'), implode(',', $this->allowedArgs));
                    break;
            }
        } catch (Exception $e) {
            $message = __('Something went wrong. Contact support if problem persists.');
        }
        return $message;
    }
}
