<?php


namespace App\Domain\Jabber;

/**
 * Class Message
 * @package App\Domain\Jabber
 */
class Message
{
    /**
     * @var string
     */
    private string $command;
    /**
     * @var array
     */
    private array $args;

    /**
     * Message constructor.
     * @param $command
     * @param array $args
     */
    public function __construct($command, array $args)
    {
        $this->command = $command;
        $this->args = $args;
    }

    /**
     * @return string
     */
    public function command()
    {
        return $this->command;
    }

    /**
     * @return array
     */
    public function args()
    {
        return $this->args;
    }
}
