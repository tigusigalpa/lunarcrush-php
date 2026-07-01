<?php

declare(strict_types=1);

namespace Tigusigalpa\LunarCrush\Exceptions;

use Throwable;

/**
 * Thrown when the LunarCrush API returns a non-2xx response that does not
 * map to a more specific exception (e.g. a 400 or 500 response).
 */
class ApiException extends LunarCrushException
{
    /**
     * @param string               $message      Human-readable error message.
     * @param int                  $statusCode   HTTP status code returned by the API.
     * @param array<string, mixed> $responseBody Decoded JSON response body, if available.
     * @param Throwable|null       $previous     Previous exception used for chaining.
     */
    public function __construct(
        string $message,
        public readonly int $statusCode = 0,
        public readonly array $responseBody = [],
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $statusCode, $previous);
    }

    /**
     * Build an instance from an HTTP status code and a decoded response body.
     *
     * @param array<string, mixed> $body
     */
    public static function fromResponse(int $statusCode, array $body, ?Throwable $previous = null): static
    {
        $message = (string) ($body['message'] ?? $body['error'] ?? "LunarCrush API request failed with status {$statusCode}");

        return new static($message, $statusCode, $body, $previous);
    }
}
