<?php

declare(strict_types=1);

namespace Tigusigalpa\LunarCrush;

/**
 * Immutable configuration object for the LunarCrush SDK.
 *
 * Holds the API key, base URL, and HTTP/retry tuning parameters used by
 * {@see LunarCrushClient} to build and send requests.
 */
final class LunarCrushConfig
{
    /**
     * @param string $apiKey        LunarCrush API v4 bearer token.
     * @param string $baseUrl       Base URL of the LunarCrush API (no trailing slash).
     * @param float  $timeout       Request timeout in seconds.
     * @param int    $retryAttempts Number of automatic retries on HTTP 429 responses.
     * @param float  $retryDelay    Base delay (in seconds) for exponential backoff between retries.
     */
    public function __construct(
        public readonly string $apiKey,
        public readonly string $baseUrl = 'https://lunarcrush.com/api4',
        public readonly float $timeout = 15.0,
        public readonly int $retryAttempts = 3,
        public readonly float $retryDelay = 1.0,
    ) {
    }

    /**
     * Create a configuration instance from a plain associative array.
     *
     * Recognized keys: `api_key`, `base_url`, `timeout`, `retry_attempts`, `retry_delay`.
     *
     * @param array<string, mixed> $config
     */
    public static function fromArray(array $config): self
    {
        return new self(
            apiKey: (string) ($config['api_key'] ?? ''),
            baseUrl: rtrim((string) ($config['base_url'] ?? 'https://lunarcrush.com/api4'), '/'),
            timeout: (float) ($config['timeout'] ?? 15.0),
            retryAttempts: (int) ($config['retry_attempts'] ?? 3),
            retryDelay: (float) ($config['retry_delay'] ?? 1.0),
        );
    }

    /**
     * Create a configuration instance from environment variables.
     *
     * Recognized variables: `LUNARCRUSH_API_KEY`, `LUNARCRUSH_BASE_URL`,
     * `LUNARCRUSH_TIMEOUT`, `LUNARCRUSH_RETRY_ATTEMPTS`, `LUNARCRUSH_RETRY_DELAY`.
     */
    public static function fromEnv(): self
    {
        return self::fromArray([
            'api_key' => getenv('LUNARCRUSH_API_KEY') ?: '',
            'base_url' => getenv('LUNARCRUSH_BASE_URL') ?: 'https://lunarcrush.com/api4',
            'timeout' => getenv('LUNARCRUSH_TIMEOUT') ?: 15.0,
            'retry_attempts' => getenv('LUNARCRUSH_RETRY_ATTEMPTS') ?: 3,
            'retry_delay' => getenv('LUNARCRUSH_RETRY_DELAY') ?: 1.0,
        ]);
    }
}
