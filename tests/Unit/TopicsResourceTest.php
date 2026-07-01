<?php

declare(strict_types=1);

namespace Tigusigalpa\LunarCrush\Tests\Unit;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Tigusigalpa\LunarCrush\Collections\TimeSeriesCollection;
use Tigusigalpa\LunarCrush\Collections\TopicCollection;
use Tigusigalpa\LunarCrush\Dto\TimeSeriesPointDto;
use Tigusigalpa\LunarCrush\Dto\TopicDto;
use Tigusigalpa\LunarCrush\LunarCrushClient;
use Tigusigalpa\LunarCrush\LunarCrushConfig;

final class TopicsResourceTest extends TestCase
{
    /** @var array<int, array<string, mixed>> */
    private array $history = [];

    /**
     * @param list<Response> $responses
     */
    private function makeClient(array $responses): LunarCrushClient
    {
        $this->history = [];

        $mock = new MockHandler($responses);
        $stack = HandlerStack::create($mock);
        $stack->push(Middleware::history($this->history));

        $guzzle = new GuzzleClient(['handler' => $stack]);
        $config = new LunarCrushConfig(apiKey: 'test-key', retryAttempts: 0, retryDelay: 0.0);

        return new LunarCrushClient($config, $guzzle);
    }

    public function testTimeSeriesBuildsExpectedRequestAndHydratesCollection(): void
    {
        $body = json_encode([
            'data' => [
                ['time' => 1_700_000_000, 'interactions' => 1000, 'sentiment' => 72.5],
                ['time' => 1_700_003_600, 'interactions' => 1200, 'sentiment' => 74.1],
            ],
        ], JSON_THROW_ON_ERROR);

        $client = $this->makeClient([new Response(200, [], $body)]);

        $result = $client->topics()->timeSeries('bitcoin')->interval('1w')->bucket('hour')->get();

        self::assertInstanceOf(TimeSeriesCollection::class, $result);
        self::assertCount(2, $result);
        self::assertInstanceOf(TimeSeriesPointDto::class, $result->first());
        self::assertSame(1_700_000_000, $result->first()->time);

        /** @var \Psr\Http\Message\RequestInterface $request */
        $request = $this->history[0]['request'];
        self::assertSame('/api4/public/topic/bitcoin/time-series/v1', $request->getUri()->getPath());

        parse_str($request->getUri()->getQuery(), $query);
        self::assertSame('1w', $query['interval']);
        self::assertSame('hour', $query['bucket']);
    }

    public function testListTopicsIsHydratedAsCollection(): void
    {
        $body = json_encode([
            'data' => [
                ['topic' => 'bitcoin', 'title' => 'Bitcoin'],
                ['topic' => 'ethereum', 'title' => 'Ethereum'],
            ],
        ], JSON_THROW_ON_ERROR);

        $client = $this->makeClient([new Response(200, [], $body)]);

        $result = $client->topics()->list()->get();

        self::assertInstanceOf(TopicCollection::class, $result);
        self::assertInstanceOf(TopicDto::class, $result->first());
        self::assertSame('bitcoin', $result->first()->topic);
    }

    public function testSingleTopicSummary(): void
    {
        $body = json_encode([
            'data' => ['topic' => 'bitcoin', 'title' => 'Bitcoin', 'interactions_24h' => 500000],
        ], JSON_THROW_ON_ERROR);

        $client = $this->makeClient([new Response(200, [], $body)]);

        $topic = $client->topics()->topic('bitcoin')->get();

        self::assertInstanceOf(TopicDto::class, $topic);
        self::assertSame(500000, $topic->interactions24h);

        /** @var \Psr\Http\Message\RequestInterface $request */
        $request = $this->history[0]['request'];
        self::assertSame('/api4/public/topic/bitcoin/v1', $request->getUri()->getPath());
    }
}
