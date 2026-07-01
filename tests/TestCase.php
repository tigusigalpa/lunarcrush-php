<?php

declare(strict_types=1);

namespace Tigusigalpa\LunarCrush\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Tigusigalpa\LunarCrush\Laravel\LunarCrushServiceProvider;

/**
 * Base test case for Laravel-integrated (Feature) tests, powered by Orchestra Testbench.
 */
abstract class TestCase extends BaseTestCase
{
    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return list<class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [LunarCrushServiceProvider::class];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('lunarcrush.api_key', 'test-key');
    }
}
