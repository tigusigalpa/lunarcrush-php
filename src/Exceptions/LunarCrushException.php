<?php

declare(strict_types=1);

namespace Tigusigalpa\LunarCrush\Exceptions;

use RuntimeException;

/**
 * Base exception for all errors raised by the LunarCrush SDK.
 *
 * Every exception thrown by this package extends this class, allowing
 * consumers to catch a single type to handle any SDK-related failure.
 */
class LunarCrushException extends RuntimeException
{
}
