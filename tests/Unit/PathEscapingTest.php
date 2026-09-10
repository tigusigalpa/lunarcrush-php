<?php

declare(strict_types=1);

namespace Tigusigalpa\LunarCrush\Tests\Unit;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Tigusigalpa\LunarCrush\LunarCrushClient;
use Tigusigalpa\LunarCrush\LunarCrushConfig;
use Tigusigalpa\LunarCrush\Resources\AbstractResource;

final class PathEscapingTest extends TestCase
{
    public function testEveryDynamicEndpointEscapesUserSuppliedPathSegments(): void
    {
        /** @var list<array<string, mixed>> $history */
        $history = [];
        $unsafeValue = 'value/with?reserved#characters';
        $encodedValue = rawurlencode($unsafeValue);

        /** @var array<string, callable(LunarCrushClient, string): AbstractResource> $calls */
        $calls = [
            '/public/ai/topic/%s' => static fn (LunarCrushClient $client, string $value): AbstractResource => $client->ai()->topic($value),
            '/public/ai/creator/%s/%s' => static fn (LunarCrushClient $client, string $value): AbstractResource => $client->ai()->creator($value, $value),
            '/public/category/%s/v1' => static fn (LunarCrushClient $client, string $value): AbstractResource => $client->categories()->category($value),
            '/public/category/%s/creators/v1' => static fn (LunarCrushClient $client, string $value): AbstractResource => $client->categories()->creators($value),
            '/public/category/%s/news/v1' => static fn (LunarCrushClient $client, string $value): AbstractResource => $client->categories()->news($value),
            '/public/category/%s/posts/v1' => static fn (LunarCrushClient $client, string $value): AbstractResource => $client->categories()->posts($value),
            '/public/category/%s/time-series/v1' => static fn (LunarCrushClient $client, string $value): AbstractResource => $client->categories()->timeSeries($value),
            '/public/category/%s/topics/v1' => static fn (LunarCrushClient $client, string $value): AbstractResource => $client->categories()->topics($value),
            '/public/coins/%s/v1' => static fn (LunarCrushClient $client, string $value): AbstractResource => $client->coins()->coin($value),
            '/public/coins/%s/meta/v1' => static fn (LunarCrushClient $client, string $value): AbstractResource => $client->coins()->meta($value),
            '/public/coins/%s/time-series/v2' => static fn (LunarCrushClient $client, string $value): AbstractResource => $client->coins()->timeSeries($value),
            '/public/creator/%s/%s/v1' => static fn (LunarCrushClient $client, string $value): AbstractResource => $client->creators()->creator($value, $value),
            '/public/creator/%s/%s/posts/v1' => static fn (LunarCrushClient $client, string $value): AbstractResource => $client->creators()->posts($value, $value),
            '/public/creator/%s/%s/time-series/v1' => static fn (LunarCrushClient $client, string $value): AbstractResource => $client->creators()->timeSeries($value, $value),
            '/public/searches/%s' => static fn (LunarCrushClient $client, string $value): AbstractResource => $client->searches()->show($value),
            '/public/searches/%s/update' => static fn (LunarCrushClient $client, string $value): AbstractResource => $client->searches()->update($value),
            '/public/searches/%s/delete' => static fn (LunarCrushClient $client, string $value): AbstractResource => $client->searches()->delete($value),
            '/public/stocks/%s/v1' => static fn (LunarCrushClient $client, string $value): AbstractResource => $client->stocks()->stock($value),
            '/public/stocks/%s/time-series/v2' => static fn (LunarCrushClient $client, string $value): AbstractResource => $client->stocks()->timeSeries($value),
            '/public/topic/%s/v1' => static fn (LunarCrushClient $client, string $value): AbstractResource => $client->topics()->topic($value),
            '/public/topic/%s/time-series/v1' => static fn (LunarCrushClient $client, string $value): AbstractResource => $client->topics()->timeSeries($value),
            '/public/topic/%s/time-series/v2' => static fn (LunarCrushClient $client, string $value): AbstractResource => $client->topics()->timeSeriesV2($value),
            '/public/topic/%s/creators/v1' => static fn (LunarCrushClient $client, string $value): AbstractResource => $client->topics()->creators($value),
            '/public/topic/%s/news/v1' => static fn (LunarCrushClient $client, string $value): AbstractResource => $client->topics()->news($value),
            '/public/topic/%s/posts/v1' => static fn (LunarCrushClient $client, string $value): AbstractResource => $client->topics()->posts($value),
            '/public/topic/%s/whatsup/v1' => static fn (LunarCrushClient $client, string $value): AbstractResource => $client->topics()->whatsUp($value),
        ];

        $mock = new MockHandler(array_map(
            static fn (): Response => new Response(200, ['Content-Type' => 'application/json'], '{}'),
            $calls,
        ));
        $stack = HandlerStack::create($mock);
        $stack->push(Middleware::history($history));

        $client = new LunarCrushClient(
            new LunarCrushConfig(apiKey: 'test-key', retryAttempts: 0),
            new GuzzleClient(['handler' => $stack]),
        );

        foreach ($calls as $call) {
            $call($client, $unsafeValue)->raw();
        }

        self::assertCount(count($calls), $history);

        foreach (array_values($calls) as $index => $_) {
            $template = array_keys($calls)[$index];
            /** @var RequestInterface $request */
            $request = $history[$index]['request'];

            self::assertSame('/api4' . sprintf($template, $encodedValue, $encodedValue), $request->getUri()->getPath());
            self::assertSame('', $request->getUri()->getQuery());
            self::assertSame('', $request->getUri()->getFragment());
        }
    }
}
