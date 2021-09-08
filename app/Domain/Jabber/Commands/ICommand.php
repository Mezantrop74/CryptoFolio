<?php

namespace App\Domain\Jabber\Commands;

/**
 * Interface ICommand
 * @package App\Domain\Jabber\Commands
 */
interface ICommand
{
    /**
     * @return string
     */
    function response(): string;

    /**
     * @return string
     */
    static function description(): string;
}
