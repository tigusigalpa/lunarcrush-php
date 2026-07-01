<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| LunarCrush API Configuration
|--------------------------------------------------------------------------
|
| This file defines the configuration used by the LunarCrush Laravel
| integration to build the underlying LunarCrushClient instance.
|
*/

return [

    /*
    |--------------------------------------------------------------------------
    | API Key
    |--------------------------------------------------------------------------
    |
    | Your LunarCrush API v4 bearer token. Generate one from your LunarCrush
    | account dashboard. It is sent as `Authorization: Bearer <api_key>`.
    |
    */
    'api_key' => env('LUNARCRUSH_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    */
    'base_url' => env('LUNARCRUSH_BASE_URL', 'https://lunarcrush.com/api4'),

    /*
    |--------------------------------------------------------------------------
    | Timeout
    |--------------------------------------------------------------------------
    |
    | Request timeout in seconds.
    |
    */
    'timeout' => (float) env('LUNARCRUSH_TIMEOUT', 15.0),

    /*
    |--------------------------------------------------------------------------
    | Retry Attempts
    |--------------------------------------------------------------------------
    |
    | Number of automatic retries performed with exponential backoff when a
    | 429 Too Many Requests response is received.
    |
    */
    'retry_attempts' => (int) env('LUNARCRUSH_RETRY_ATTEMPTS', 3),

    /*
    |--------------------------------------------------------------------------
    | Retry Delay
    |--------------------------------------------------------------------------
    |
    | Base delay in seconds used for the exponential backoff strategy.
    | The delay for attempt N is: retry_delay * (2 ^ (N - 1)).
    |
    */
    'retry_delay' => (float) env('LUNARCRUSH_RETRY_DELAY', 1.0),

];
