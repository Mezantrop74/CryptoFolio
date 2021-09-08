<?php

namespace App\Domain\Ticker;

/**
 * Class Tickers
 * @package App\Domain\Ticker
 */
class Tickers
{
    /**
     *
     */
    const CMC = 0;
    /**
     *
     */
    const ETHERSCAN = 1;
    /**
     *
     */
    const CUSTOM = 2;

    /**
     * @return int
     */
    public static function default()
    {
        return self::CMC;
    }
}
