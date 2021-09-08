<?php

namespace App\Domain\Settings;

use Spatie\LaravelSettings\Settings;

/**
 * Class ApiSettings
 * @package App\Domain\Settings
 */
class ApiSettings extends Settings
{
    /**
     * @var string
     */
    public string $cmc_api_token;
    public string $cryptoapis_api_token;

    /**
     * @return string
     */
    public static function group(): string
    {
        return 'api';
    }

    /**
     * @return string[]
     */
    public static function encrypted(): array
    {
        return [
            'cmc_api_token',
            'cryptoapis_api_token'
        ];
    }
}
