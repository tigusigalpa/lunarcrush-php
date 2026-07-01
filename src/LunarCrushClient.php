<?php

declare(strict_types=1);

namespace Tigusigalpa\LunarCrush;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\HttpFactory;
use JsonException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Tigusigalpa\LunarCrush\Exceptions\ApiException;
use Tigusigalpa\LunarCrush\Exceptions\LunarCrushException;
use Tigusigalpa\LunarCrush\Exceptions\NotFoundException;
use Tigusigalpa\LunarCrush\Exceptions\RateLimitException;
use Tigusigalpa\LunarCrush\Exceptions\UnauthorizedException;
use Tigusigalpa\LunarCrush\Resources\AiResource;
use Tigusigalpa\LunarCrush\Resources\CategoriesResource;
use Tigusigalpa\LunarCrush\Resources\CoinsResource;
use Tigusigalpa\LunarCrush\Resources\CreatorsResource;
use Tigusigalpa\LunarCrush\Resources\PostsResource;
use Tigusigalpa\LunarCrush\Resources\SearchesResource;
use Tigusigalpa\LunarCrush\Resources\StocksResource;
use Tigusigalpa\LunarCrush\Resources\SystemResource;
use Tigusigalpa\LunarCrush\Resources\TopicsResource;

/**
 * Framework-agnostic HTTP client for the LunarCrush API v4.
 *
 * Handles request signing (Bearer authentication), JSON encoding/decoding,
 * automatic exponential-backoff retries on rate limiting, and mapping of
 * non-2xx responses to the SDK's exception hierarchy.
 *
 * Any PSR-18 compatible HTTP client may be injected via the constructor;
 * Guzzle is used by default.
 */
final class LunarCrushClient
{
    private ClientInterface $httpClient;

    private RequestFactoryInterface $requestFactory;

    private StreamFactoryInterface $streamFactory;

    private UriFactoryInterface $uriFactory;

    /**
     * @param LunarCrushConfig             $config         SDK configuration (API key, base URL, retry policy, etc.).
     * @param ClientInterface|null         $httpClient     Any PSR-18 compatible HTTP client. Defaults to Guzzle.
     * @param RequestFactoryInterface|null $requestFactory PSR-17 request factory. Defaults to Guzzle's HttpFactory.
     * @param StreamFactoryInterface|null  $streamFactory  PSR-17 stream factory. Defaults to Guzzle's HttpFactory.
     * @param UriFactoryInterface|null     $uriFactory     PSR-17 URI factory. Defaults to Guzzle's HttpFactory.
     */
    public function __construct(
        private readonly LunarCrushConfig $config,
        ?ClientInterface $httpClient = null,
        ?RequestFactoryInterface $requestFactory = null,
        ?StreamFactoryInterface $streamFactory = null,
        ?UriFactoryInterface $uriFactory = null,
    ) {
        $factory = new HttpFactory();

        $this->httpClient = $httpClient ?? new GuzzleClient(['timeout' => $config->timeout]);
        $this->requestFactory = $requestFactory ?? $factory;
        $this->streamFactory = $streamFactory ?? $factory;
        $this->uriFactory = $uriFactory ?? $factory;
    }

    /**
     * Convenience factory for standalone (non-Laravel) usage.
     *
     * @param string               $apiKey  LunarCrush API v4 bearer token.
     * @param array<string, mixed> $options Optional overrides: base_url, timeout, retry_attempts, retry_delay.
     */
    public static function make(string $apiKey, array $options = []): self
    {
        return new self(LunarCrushConfig::fromArray(['api_key' => $apiKey] + $options));
    }

    /** Access the Coins resource group. */
    public function coins(): CoinsResource
    {
        return new CoinsResource($this);
    }

    /** Access the Topics resource group. */
    public function topics(): TopicsResource
    {
        return new TopicsResource($this);
    }

    /** Access the Categories resource group. */
    public function categories(): CategoriesResource
    {
        return new CategoriesResource($this);
    }

    /** Access the Creators resource group. */
    public function creators(): CreatorsResource
    {
        return new CreatorsResource($this);
    }

    /** Access the Posts resource group. */
    public function posts(): PostsResource
    {
        return new PostsResource($this);
    }

    /** Access the Stocks resource group. */
    public function stocks(): StocksResource
    {
        return new StocksResource($this);
    }

    /** Access the Searches (custom aggregations) resource group. */
    public function searches(): SearchesResource
    {
        return new SearchesResource($this);
    }

    /** Access the System resource group. */
    public function system(): SystemResource
    {
        return new SystemResource($this);
    }

    /** Access the AI & MCP resource group. */
    public function ai(): AiResource
    {
        return new AiResource($this);
    }

    /**
     * Send a GET request to the LunarCrush API and return the decoded JSON body.
     *
     * Automatically retries with exponential backoff when a 429 response is
     * received, up to `retry_attempts` times, before throwing {@see RateLimitException}.
     *
     * @param string               $path  API path, e.g. `/public/coins/list/v1`.
     * @param array<string, mixed> $query Query string parameters.
     *
     * @return array<string, mixed> Decoded JSON response body.
     *
     * @throws LunarCrushException
     */
    public function request(string $method, string $path, array $query = []): array
    {
        $attempt = 0;
        $maxAttempts = max(0, $this->config->retryAttempts);

        while (true) {
            try {
                return $this->send($method, $path, $query);
            } catch (RateLimitException $e) {
                if ($attempt >= $maxAttempts) {
                    throw $e;
                }

                $delay = $e->retryAfter ?? (int) ($this->config->retryDelay * (2 ** $attempt));
                if ($delay > 0) {
                    usleep((int) ($delay * 1_000_000));
                }

                $attempt++;
            }
        }
    }

    /**
     * @param array<string, mixed> $query
     *
     * @return array<string, mixed>
     */
    private function send(string $method, string $path, array $query): array
    {
        $uri = $this->uriFactory
            ->createUri(rtrim($this->config->baseUrl, '/') . '/' . ltrim($path, '/'));

        $filteredQuery = array_filter($query, static fn ($value) => $value !== null);
        if ($filteredQuery !== []) {
            $uri = $uri->withQuery(http_build_query($filteredQuery, '', '&', PHP_QUERY_RFC3986));
        }

        $request = $this->requestFactory
            ->createRequest($method, $uri)
            ->withHeader('Authorization', 'Bearer ' . $this->config->apiKey)
            ->withHeader('Accept', 'application/json');

        try {
            $response = $this->httpClient->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
            throw new ApiException('LunarCrush API request failed: ' . $e->getMessage(), 0, [], $e);
        }

        $statusCode = $response->getStatusCode();
        $rawBody = (string) $response->getBody();

        $body = [];
        if ($rawBody !== '') {
            try {
                /** @var array<string, mixed> $body */
                $body = json_decode($rawBody, true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException $e) {
                if ($statusCode >= 200 && $statusCode < 300) {
                    throw new ApiException('Failed to decode LunarCrush API response: ' . $e->getMessage(), $statusCode, [], $e);
                }
                $body = ['message' => $rawBody];
            }
        }

        if ($statusCode >= 200 && $statusCode < 300) {
            return $body;
        }

        throw match ($statusCode) {
            401 => new UnauthorizedException((string) ($body['message'] ?? 'Unauthorized: invalid or missing LunarCrush API key.'), $body),
            404 => new NotFoundException((string) ($body['message'] ?? 'The requested LunarCrush resource was not found.'), $body),
            429 => new RateLimitException(
                (string) ($body['message'] ?? 'LunarCrush API rate limit exceeded.'),
                $body,
                $this->parseRetryAfter($response->getHeaderLine('Retry-After')),
            ),
            default => ApiException::fromResponse($statusCode, $body),
        };
    }

    private function parseRetryAfter(string $header): ?int
    {
        if ($header === '' || !is_numeric($header)) {
            return null;
        }

        return (int) $header;
    }
}
