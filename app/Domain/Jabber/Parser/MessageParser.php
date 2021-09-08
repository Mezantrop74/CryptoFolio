<?php


use App\Domain\Jabber\Message;

/**
 * Class MessageParser
 */
class MessageParser
{
    /**
     * @param $response
     * @return Message
     */
    public static function parse($response)
    {
        $messages = [];
        $regex = "/(?<=<body>)(.*\n?)(?=<\/body>)/";
        preg_match($regex, $response, $messages);
        $message = $messages[0] ?? '';
        if (!isset($message) || strlen($message) == 0 || $message[0] != '!') {
            throw new BadMethodCallException('Not valid command: ' . $message ?? '');
        }
        $args = array_map('trim', explode(" ", $message));
        return new Message($args[0], array_slice($args, 1));
    }
}
