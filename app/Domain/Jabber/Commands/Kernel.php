<?php


namespace App\Domain\Jabber\Commands;


use App\Domain\Jabber\Commands\Pool\AssetCommand;
use App\Domain\Jabber\Commands\Pool\HelpCommand;
use App\Domain\Jabber\Commands\Pool\MyAssetsCommand;
use App\Domain\Jabber\Commands\Pool\PingCommand;
use App\Domain\Jabber\Commands\Pool\RateCommand;
use App\Domain\Jabber\Commands\Pool\WalletCommand;

/**
 * Class Kernel
 * @package App\Domain\Jabber\Commands
 */
class Kernel
{
    /**
     * @var string[]
     */
    private static $commands = [
        'wallet' => WalletCommand::class,
        'asset' => AssetCommand::class,
        'my' => MyAssetsCommand::class,
        'ping' => PingCommand::class,
        'rate' => RateCommand::class,
        'help' => HelpCommand::class,
    ];

    /**
     * @return string[]
     */
    public static function commands(): array
    {
        return static::$commands;
    }
}
