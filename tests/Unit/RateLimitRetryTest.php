<?php

declare(strict_types=1);

namespace Tigusigalpa\LunarCrush\Tests\Unit;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Tigusigalpa\LunarCrush\Dto\CoinDto;
use Tigusigalpa\LunarCrush\Exceptions\RateLimitException;
use Tigusigalpa\LunarCrush\Exceptions\UnauthorizedException;
use Tigusigalpa\LunarCrush\Exceptions\NotFoundException;
use Tigusigalpa\LunarCrush\LunarCrushClient;
use Tigusigalpa\LunarCrush\LunarCrushConfig;

final class RateLimitRetryTest extends TestCase
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

    public function testRetriesOnRateLimitAndEventuallySucceeds(): void
    {
        $successBody = json_encode([
            'data' => ['id' => 1, 'symbol' => 'BTC', 'name' => 'Bitcoin'],
        ], JSON_THROW_ON_ERROR);

        $client = $this->makeClient([
            new Response(429, ['Retry-After' => '0'], json_encode(['message' => 'Too many requests'])),
            new Response(429, ['Retry-After' => '0'], json_encode(['message' => 'Too many requests'])),
            new Response(200, [], $successBody),
        ], retryAttempts: 3, retryDelay: 0.0);

        $coin = $client->coins()->coin('bitcoin')->get();

        self::assertInstanceOf(CoinDto::class, $coin);
        self::assertSame('Bitcoin', $coin->name);
        self::assertCount(3, $this->history);
    }

    public function testThrowsRateLimitExceptionAfterExhaustingRetries(): void
    {
        $responses = array_fill(
            0,
            3,
            new Response(429, [], json_encode(['message' => 'Too many requests'])),
        );

        $client = $this->makeClient($responses, retryAttempts: 2, retryDelay: 0.0);

        $this->expectException(RateLimitException::class);

        try {
            $client->coins()->coin('bitcoin')->get();
        } finally {
            self::assertCount(3, $this->history);
        }
    }

    public function testUnauthorizedResponseThrowsUnauthorizedException(): void
    {
        $client = $this->makeClient([
            new Response(401, [], json_encode(['message' => 'Invalid API key'])),
        ]);

        $this->expectException(UnauthorizedException::class);

        $client->coins()->coin('bitcoin')->get();
    }

    public function testNotFoundResponseThrowsNotFoundException(): void
    {
        $client = $this->makeClient([
            new Response(404, [], json_encode(['message' => 'Coin not found'])),
        ]);

        $this->expectException(NotFoundException::class);

        $client->coins()->coin('does-not-exist')->get();
    }
}
