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
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'vue' => [
        'MIX_VUE_APP_TOKEN' => env('MIX_VUE_APP_TOKEN'),
        'MIX_FIREBASE_API_KEY' => env('MIX_FIREBASE_API_KEY'),
        'MIX_FIREBASE_APP_ID' => env('MIX_FIREBASE_APP_ID'),
        'MIX_GOOGLE_MAP_API_KEY' => env('MIX_GOOGLE_MAP_API_KEY'),
        'MIX_APP_URL' => env('MIX_APP_URL'),
    ],

    'telegram-bot-api' => [
        'token' => env('TELEGRAM_BOT_TOKEN', env('TELEGRAM_BOT_TOKEN'))
    ],



    'firebase' => [
        'server_key' => env('FIREBASE_SERVER_KEY'),
    ],

    'africastalking' => [
        'username' => env('AFST_USERNAME'),
        'api_key' => env('AFRICAISTALKING_APIKEY_LIVE'),
    ],

];
