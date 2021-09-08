<?php


namespace App\Domain\Jabber\Commands;


use App\Domain\Jabber\Message;
use App\Models\User;

/**
 * Class Command
 * @package App\Domain\Jabber\Commands
 */
abstract class Command implements ICommand
{
    /**
     * @var Message
     */
    protected Message $message;
    /**
     * @var User
     */
    protected User $user;
    /**
     * @var string
     */
    protected static string $description = '';

    /**
     * Command constructor.
     * @param Message $message
     * @param User $user
     */
    public function __construct(Message $message, User $user)
    {
        $this->message = $message;
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->response();
    }

    /**
     * @return string
     */
    public function response(): string
    {
        return '';
    }

    /**
     * @return User
     */
    public function user()
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public static function description(): string
    {
        return __(static::$description);
    }
}
