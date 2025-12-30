<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Fairu API Base URL
    |--------------------------------------------------------------------------
    |
    | The base URL for the Fairu GraphQL API. You can use different URLs
    | for different environments (production, staging, local development).
    |
    | Examples:
    | - https://fairu.app (Production)
    | - https://fairu.dev (Staging)
    | - http://localhost:8000 (Local)
    |
    */
    'base_url' => env('FAIRU_URL', 'https://fairu.app'),

    /*
    |--------------------------------------------------------------------------
    | File Proxy URL
    |--------------------------------------------------------------------------
    |
    | The base URL for the Fairu File Proxy service used for image
    | transformations and file delivery.
    |
    */
    'file_proxy_url' => env('FAIRU_FILE_PROXY_URL', 'https://files.fairu.app'),

    /*
    |--------------------------------------------------------------------------
    | API Token
    |--------------------------------------------------------------------------
    |
    | Your Fairu API Bearer Token for authentication.
    |
    */
    'token' => env('FAIRU_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | HTTP Client Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for the HTTP client used to communicate with the API.
    |
    */
    'timeout' => env('FAIRU_TIMEOUT', 30),

    'retry' => [
        'times' => env('FAIRU_RETRY_TIMES', 3),
        'sleep' => env('FAIRU_RETRY_SLEEP', 100),
        'when' => null, // Callable to determine if retry should happen
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    |
    | Configure caching behavior for API responses.
    |
    */
    'cache' => [
        'enabled' => env('FAIRU_CACHE_ENABLED', true),
        'store' => env('FAIRU_CACHE_STORE', null), // null = default store
        'prefix' => 'fairu_',

        // Default TTL in seconds per resource type
        'ttl' => [
            'tenant' => 3600,      // 1 hour
            'roles' => 3600,       // 1 hour
            'users' => 1800,       // 30 minutes
            'copyrights' => 1800,  // 30 minutes
            'licenses' => 1800,    // 30 minutes
            'workflows' => 1800,   // 30 minutes
            'disks' => 1800,       // 30 minutes
            'galleries' => 600,    // 10 minutes
            'folders' => 300,      // 5 minutes
            'assets' => 300,       // 5 minutes
            'default' => 600,      // 10 minutes
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | Enable logging of API requests and responses for debugging.
    |
    */
    'logging' => [
        'enabled' => env('FAIRU_LOGGING_ENABLED', false),
        'channel' => env('FAIRU_LOG_CHANNEL', null), // null = default channel
    ],
];
