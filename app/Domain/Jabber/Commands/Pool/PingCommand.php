<?php


namespace App\Domain\Jabber\Commands\Pool;


use App\Domain\Jabber\Commands\Command;
use App\Domain\Jabber\Commands\ICommand;
use App\Domain\Jabber\Message;
use App\Models\User;

/**
 * Class PingCommand
 * @package App\Domain\Jabber\Commands\Pool
 */
class PingCommand extends Command implements ICommand
{
    /**
     * @var string
     */
    protected static string $description = 'Sending Hello message (For testing purposes). No arguments.';

    /**
     * PingCommand constructor.
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
        return __('You are welcome!');
    }
}
