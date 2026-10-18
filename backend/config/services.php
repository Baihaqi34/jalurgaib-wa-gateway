<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Go Microservice
    |--------------------------------------------------------------------------
    */
    'wa_service' => [
        'url'     => env('WA_SERVICE_URL', 'http://localhost:8080'),
        'api_key' => env('WA_SERVICE_API_KEY', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Kasera Payment Gateway
    |--------------------------------------------------------------------------
    */
    'kasera' => [
        'key'                => env('KASERA_PAY_KEY'),
        'base_url'           => env('KASERA_PAY_BASE_URL', 'https://pay.kasera.id/v1'),
        'webhook_secret'     => env('KASERA_PAY_WEBHOOK_SECRET'),
        'expires_in_minutes' => (int) env('KASERA_PAY_EXPIRES_IN_MINUTES', 60),
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase Authentication (Google Sign-In)
    |--------------------------------------------------------------------------
    */
    'firebase' => [
        'api_key'             => env('FIREBASE_API_KEY', ''),
        'auth_domain'         => env('FIREBASE_AUTH_DOMAIN', ''),
        'project_id'          => env('FIREBASE_PROJECT_ID', ''),
        'storage_bucket'      => env('FIREBASE_STORAGE_BUCKET', ''),
        'messaging_sender_id' => env('FIREBASE_MESSAGING_SENDER_ID', ''),
        'app_id'              => env('FIREBASE_APP_ID', ''),
    ],

];
