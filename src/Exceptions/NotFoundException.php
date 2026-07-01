<?php

declare(strict_types=1);

namespace Tigusigalpa\LunarCrush\Exceptions;

use Throwable;

/**
 * Thrown when the LunarCrush API responds with HTTP 404 (Not Found),
 * indicating the requested resource (topic, coin, creator, etc.) does not exist.
 */
class NotFoundException extends ApiException
{
    /**
     * @param string               $message      Human-readable error message.
     * @param array<string, mixed> $responseBody Decoded JSON response body, if available.
     * @param Throwable|null       $previous     Previous exception used for chaining.
     */
    public function __construct(
        string $message = 'The requested LunarCrush resource was not found.',
        array $responseBody = [],
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, 404, $responseBody, $previous);
    }
}
