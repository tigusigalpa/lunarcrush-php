<?php

declare(strict_types=1);

namespace Tigusigalpa\LunarCrush\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tigusigalpa\LunarCrush\LunarCrushConfig;

final class LunarCrushConfigTest extends TestCase
{
    public function testFromEnvPreservesZeroValuedRetrySettings(): void
    {
        $names = ['LUNARCRUSH_RETRY_ATTEMPTS', 'LUNARCRUSH_RETRY_DELAY'];
        $previousValues = [];

        foreach ($names as $name) {
            $previousValues[$name] = getenv($name);
        }

        try {
            putenv('LUNARCRUSH_RETRY_ATTEMPTS=0');
            putenv('LUNARCRUSH_RETRY_DELAY=0');

            $config = LunarCrushConfig::fromEnv();

            self::assertSame(0, $config->retryAttempts);
            self::assertSame(0.0, $config->retryDelay);
        } finally {
            foreach ($previousValues as $name => $value) {
                putenv($value === false ? $name : $name . '=' . $value);
            }
        }
    }
}
