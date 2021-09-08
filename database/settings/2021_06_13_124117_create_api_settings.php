<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateApiSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->addEncrypted('api.cmc_api_token', '');
    }
}
