<?php


namespace App\Domain\Jabber\Commands\Pool;


use App\Domain\Jabber\Commands\Command;
use App\Domain\Jabber\Commands\ICommand;
use App\Domain\Jabber\Commands\Kernel;
use App\Domain\Jabber\Message;
use App\Models\User;

/**
 * Class HelpCommand
 * @package App\Domain\Jabber\Commands\Pool
 */
class HelpCommand extends Command implements ICommand
{
    /**
     * @var string
     */
    protected static string $description = 'Sending info about all available bot commands. No arguments.';

    /**
     * HelpCommand constructor.
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
        $commands = [];
        foreach (Kernel::commands() as $command => $class) {
            $commands[] = sprintf("!%s - %s", $command, $class::description());
        }
        return implode("\n", $commands);
    }
}
