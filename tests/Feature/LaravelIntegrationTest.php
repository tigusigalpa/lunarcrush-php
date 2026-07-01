<?php

declare(strict_types=1);

namespace Tigusigalpa\LunarCrush\Tests\Feature;

use Tigusigalpa\LunarCrush\Laravel\LunarCrushFacade;
use Tigusigalpa\LunarCrush\LunarCrushClient;
use Tigusigalpa\LunarCrush\Resources\CoinsResource;
use Tigusigalpa\LunarCrush\Resources\TopicsResource;
use Tigusigalpa\LunarCrush\Tests\TestCase;

final class LaravelIntegrationTest extends TestCase
{
    public function testConfigFileIsMerged(): void
    {
        self::assertSame('https://lunarcrush.com/api4', config('lunarcrush.base_url'));
        self::assertSame(3, config('lunarcrush.retry_attempts'));
        self::assertSame(1.0, config('lunarcrush.retry_delay'));
        self::assertSame('test-key', config('lunarcrush.api_key'));
    }

    public function testServiceProviderBindsSharedClientInstance(): void
    {
        $client = $this->app->make(LunarCrushClient::class);

        self::assertInstanceOf(LunarCrushClient::class, $client);
        self::assertSame($client, $this->app->make(LunarCrushClient::class));
        self::assertSame($client, $this->app->make('lunarcrush'));
    }

    public function testFacadeResolvesUnderlyingClientResources(): void
    {
        self::assertInstanceOf(CoinsResource::class, LunarCrushFacade::coins());
        self::assertInstanceOf(TopicsResource::class, LunarCrushFacade::topics());
    }
}
