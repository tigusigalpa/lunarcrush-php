<?php

declare(strict_types=1);

namespace Tigusigalpa\LunarCrush\Tests\Unit;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Tigusigalpa\LunarCrush\Collections\CoinCollection;
use Tigusigalpa\LunarCrush\Dto\CoinDto;
use Tigusigalpa\LunarCrush\LunarCrushClient;
use Tigusigalpa\LunarCrush\LunarCrushConfig;

final class CoinsResourceTest extends TestCase
{
    /** @var array<int, array<string, mixed>> */
    private array $history = [];

    /**
     * @param list<Response> $responses
     */
    private function makeClient(array $responses, int $retryAttempts = 0, float $retryDelay = 0.0): LunarCrushClient
    {
        $this->history = [];

        $mock = new MockHandler($responses);
        $stack = HandlerStack::create($mock);
        $stack->push(Middleware::history($this->history));

        $guzzle = new GuzzleClient(['handler' => $stack]);
        $config = new LunarCrushConfig(
            apiKey: 'test-key',
            retryAttempts: $retryAttempts,
            retryDelay: $retryDelay,
        );

        return new LunarCrushClient($config, $guzzle);
    }

    public function testListCoinsBuildsExpectedRequestAndHydratesCollection(): void
    {
        $body = json_encode([
            'data' => [
                ['id' => 1, 'symbol' => 'BTC', 'name' => 'Bitcoin', 'galaxy_score' => 75.5],
                ['id' => 2, 'symbol' => 'ETH', 'name' => 'Ethereum', 'galaxy_score' => 68.2],
            ],
        ], JSON_THROW_ON_ERROR);

        $client = $this->makeClient([
            new Response(200, ['Content-Type' => 'application/json'], $body),
        ]);

        $result = $client->coins()->list()->sortBy('galaxy_score')->limit(50)->desc()->get();

        self::assertInstanceOf(CoinCollection::class, $result);
        self::assertCount(2, $result);
        self::assertInstanceOf(CoinDto::class, $result->first());
        self::assertSame('BTC', $result->first()->symbol);
        self::assertSame('Ethereum', $result[1]->name);

        /** @var \Psr\Http\Message\RequestInterface $request */
        $request = $this->history[0]['request'];
        self::assertSame('/api4/public/coins/list/v1', $request->getUri()->getPath());
        self::assertSame('Bearer test-key', $request->getHeaderLine('Authorization'));

        parse_str($request->getUri()->getQuery(), $query);
        self::assertSame('galaxy_score', $query['sort']);
        self::assertSame('50', $query['limit']);
        self::assertSame('true', $query['desc']);
    }

    public function testSingleCoinIsHydratedAsDto(): void
    {
        $body = json_encode([
            'data' => ['id' => 1, 'symbol' => 'BTC', 'name' => 'Bitcoin', 'price' => 65000.5],
        ], JSON_THROW_ON_ERROR);

        $client = $this->makeClient([new Response(200, [], $body)]);

        $coin = $client->coins()->coin('bitcoin')->get();

        self::assertInstanceOf(CoinDto::class, $coin);
        self::assertSame('Bitcoin', $coin->name);
        self::assertSame(65000.5, $coin->price);

        /** @var \Psr\Http\Message\RequestInterface $request */
        $request = $this->history[0]['request'];
        self::assertSame('/api4/public/coins/bitcoin/v1', $request->getUri()->getPath());
    }
}
