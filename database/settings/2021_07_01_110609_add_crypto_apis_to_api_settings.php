<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class AddCryptoApisToApiSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->addEncrypted('api.cryptoapis_api_token', '');
    }
}
