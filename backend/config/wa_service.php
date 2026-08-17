<?php

return [
    /*
    |--------------------------------------------------------------------------
    | WhatsApp Go Service Configuration
    |--------------------------------------------------------------------------
    | These settings configure how Laravel communicates with the Go whatsmeow
    | microservice for WhatsApp connectivity.
    */
    'url'     => env('WA_SERVICE_URL', 'http://localhost:8080'),
    'api_key' => env('WA_SERVICE_API_KEY', ''),
];
