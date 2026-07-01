<?php

declare(strict_types=1);

namespace Tigusigalpa\LunarCrush\Laravel;

use Illuminate\Support\ServiceProvider;
use Tigusigalpa\LunarCrush\LunarCrushClient;
use Tigusigalpa\LunarCrush\LunarCrushConfig;

/**
 * Laravel service provider for the LunarCrush SDK.
 *
 * Registers the package configuration and binds a shared {@see LunarCrushClient}
 * instance into the container, built from the `config/lunarcrush.php` values.
 */
final class LunarCrushServiceProvider extends ServiceProvider
{
    /**
     * Path to the package's default configuration file.
     */
    private function configPath(): string
    {
        return dirname(__DIR__, 2) . '/config/lunarcrush.php';
    }

    /**
     * Register bindings in the container.
     */
    public function register(): void
    {
        $this->mergeConfigFrom($this->configPath(), 'lunarcrush');

        $this->app->singleton(LunarCrushClient::class, static function ($app): LunarCrushClient {
            $config = LunarCrushConfig::fromArray((array) $app['config']->get('lunarcrush', []));

            return new LunarCrushClient($config);
        });

        $this->app->alias(LunarCrushClient::class, 'lunarcrush');
    }

    /**
     * Bootstrap package services, publishing the config file for Laravel applications.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                $this->configPath() => $this->app->configPath('lunarcrush.php'),
            ], 'lunarcrush-config');
        }
    }

    /**
     * @return list<string>
     */
    public function provides(): array
    {
        return [LunarCrushClient::class, 'lunarcrush'];
    }
}
