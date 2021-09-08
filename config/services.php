<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'jabber' => [
        'host' => env('JABBER_HOST'),
        'port' => env('JABBER_PORT') ?? 5222,
        'username' => env('JABBER_USERNAME'),
        'password' => env('JABBER_PASSWORD'),
        'resource' => env('JABBER_RESOURCE') ?? '',
        'use_tls' => env('JABBER_USE_TLS') ?? true,
        'log_enabled' => env('JABBER_LOG') ?? false,
    ],

    'cryptoapis' => [
        'endpoint' => 'https://rest.cryptoapis.io/v2/',
        'network' => 'ropsten',
    ]
];
