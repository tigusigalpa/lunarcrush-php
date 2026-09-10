# LunarCrush PHP/Laravel Client/SDK/Library

![Lunarcrush PHP Laravel SDK Client](https://i.postimg.cc/VLC7ZDZq/lunarcrush-php-laravel.jpg)

> **Real-time crypto & stock social intelligence, right where your PHP code lives.**

Ever wanted to know *what the internet is actually saying* about Bitcoin, Ethereum, or Tesla — not just the price on a
chart, but the buzz, the mood, the creators driving the conversation? That is exactly
what [LunarCrush](https://lunarcrush.com) measures across social platforms, and this SDK puts all of it a couple of
expressive lines of PHP away.

`tigusigalpa/lunarcrush-php` is a modern, framework-agnostic SDK for
the [LunarCrush API v4](https://lunarcrush.com/developers/api). It wraps **every** public endpoint — Coins, Stocks,
Topics, Categories, Creators, Posts, Searches, AI insights, and System — behind a fluent, strongly-typed interface, with
response DTOs, typed collections, automatic rate-limit retries, and first-class Laravel 10, 11, 12 & 13 integration
baked right in.

No wrestling with raw JSON. No squinting at docs to remember whether `galaxy_score` is a float. No hand-rolling yet
another retry loop. You ask a question, you get typed objects back. That's the whole idea.

```php
$coins = LunarCrush::coins()->list()->sortBy('galaxy_score')->limit(10)->desc()->get();

foreach ($coins as $coin) {
    echo "{$coin->symbol}: Galaxy Score {$coin->galaxyScore}, sentiment {$coin->sentiment}\n";
}
```

[![Packagist Version](https://img.shields.io/packagist/v/tigusigalpa/lunarcrush-php.svg)](https://packagist.org/packages/tigusigalpa/lunarcrush-php)
[![PHP Version](https://img.shields.io/packagist/php-v/tigusigalpa/lunarcrush-php.svg)](https://packagist.org/packages/tigusigalpa/lunarcrush-php)
[![License](https://img.shields.io/packagist/l/tigusigalpa/lunarcrush-php.svg)](LICENSE)
[![CI](https://github.com/tigusigalpa/lunarcrush-php/actions/workflows/ci.yml/badge.svg?branch=main)](https://github.com/tigusigalpa/lunarcrush-php/actions/workflows/ci.yml)
[![Tests](https://github.com/tigusigalpa/lunarcrush-php/actions/workflows/tests.yml/badge.svg?branch=main)](https://github.com/tigusigalpa/lunarcrush-php/actions/workflows/tests.yml)
[![Coverage](https://github.com/tigusigalpa/lunarcrush-php/actions/workflows/coverage.yml/badge.svg?branch=main)](https://github.com/tigusigalpa/lunarcrush-php/actions/workflows/coverage.yml)
[![CodeQL](https://github.com/tigusigalpa/lunarcrush-php/actions/workflows/codeql.yml/badge.svg?branch=main)](https://github.com/tigusigalpa/lunarcrush-php/actions/workflows/codeql.yml)
[![Codecov](https://codecov.io/gh/tigusigalpa/lunarcrush-php/graph/badge.svg)](https://codecov.io/gh/tigusigalpa/lunarcrush-php)
[![GitHub Release](https://img.shields.io/github/v/release/tigusigalpa/lunarcrush-php)](https://github.com/tigusigalpa/lunarcrush-php/releases)

---

## Table of Contents

- [Why this SDK?](#why-this-sdk)
- [Requirements](#requirements)
- [Features](#features)
- [Installation](#installation)
- [Configuration](#configuration)
    - [Standalone (framework-agnostic)](#standalone-framework-agnostic)
    - [Laravel](#laravel)
    - [Configuration reference](#configuration-reference)
- [Quick Start](#quick-start)
- [Cookbook: real-world recipes](#cookbook-real-world-recipes)
- [Fluent Query API](#fluent-query-api)
- [Working with Responses](#working-with-responses)
- [Advanced Usage](#advanced-usage)
- [API Reference](#api-reference)
- [Error Handling](#error-handling)
- [Testing](#testing)
- [Testing Your Own App](#testing-your-own-app)
- [FAQ](#faq)
- [Roadmap](#roadmap)
- [Contributing](#contributing)
- [License](#license)

## Why this SDK?

The LunarCrush REST API is genuinely great, but talking to any HTTP API by hand gets old fast: you build query strings,
decode JSON, invent your own value objects, and then discover — usually in production — that you forgot to handle a
`429 Too Many Requests`. This package exists so you never have to write that boilerplate again.

- **It speaks PHP, not JSON.** Responses come back as readonly DTOs and iterable collections, so your IDE autocompletes
  fields and your static analyser catches typos.
- **It is polite to the API.** When you hit a rate limit, it backs off and retries automatically — using the
  `Retry-After` header when the server sends one.
- **It gets out of your way.** The core has zero framework dependencies. Drop it into Laravel, Symfony, Slim, a plain
  script, or a queue worker — it does not care.
- **It never traps your data.** Every DTO keeps the original payload on a `raw` property, so a new API field is never a
  reason to wait for a package update.

## Requirements

| Requirement          | Version                                                                          |
|----------------------|----------------------------------------------------------------------------------|
| PHP                  | `8.1`–`8.4` are tested in CI; newer PHP versions are supported by the `^8.1` constraint |
| A PSR-18 HTTP client | Guzzle `^7.4` ships by default                                                   |
| Laravel (optional)   | `10.x`, `11.x`, `12.x`, or `13.x` for the facade & service provider              |
| A LunarCrush API key | Grab one from your [LunarCrush dashboard](https://lunarcrush.com/developers/api) |

## Features

- **Framework-agnostic core** — works in any PHP 8.1+ project, with a thin, optional Laravel 10–13 layer on top.
- **Bring your own HTTP client** — Guzzle is the default, but any [PSR-18](https://www.php-fig.org/psr/psr-18/) client (
  Symfony HttpClient, HTTPlug, etc.) drops straight in via the constructor.
- **A fluent builder that reads like a sentence** — `coins()->list()->sortBy('galaxy_score')->limit(50)->desc()->get()`.
  Chain `page()`, `interval()`, `bucket()`, `start()`, `end()`, `withParam()` and more.
- **Strongly typed, readonly DTOs** — every response hydrates into a real object: `CoinDto`, `TopicDto`, `StockDto`,
  `CreatorDto`, `PostDto`, `TimeSeriesPointDto`, `SearchDto`, `CategoryDto`.
- **Typed, iterable collections** — list endpoints return collections that implement `Countable`, `IteratorAggregate`,
  and `ArrayAccess`, with helpers like `first()`, `last()`, `filter()`, `map()`, and `toArray()`.
- **Never lose a field** — each DTO exposes the untouched API payload via its `raw` property, so newly released API
  fields are always reachable.
- **Grown-up error handling** — a clean exception hierarchy (`ApiException`, `RateLimitException`,
  `UnauthorizedException`, `NotFoundException`) plus automatic exponential-backoff retries on `429`.
- **Enum-safe creator networks** — a `Network` enum (`Twitter`, `YouTube`, `Instagram`, `Reddit`, `TikTok`) means no
  more typo'd network strings.
- **Laravel niceties** — auto-discovered service provider, a `LunarCrush` facade, and a publishable config file wired to
  your `.env`.
- **Genuinely tested** — a PHPUnit 10 suite covers happy paths, retry logic, exception mapping, DTO hydration, and the
  Laravel integration end to end.

## Installation

Install through [Composer](https://getcomposer.org/):

```bash
composer require tigusigalpa/lunarcrush-php
```

That's it. The package requires PHP `^8.1` and pulls in Guzzle as its default HTTP client. If you're on Laravel, the
service provider and facade are registered automatically through package discovery — no manual wiring needed.

## Configuration

### Standalone (framework-agnostic)

The fastest way in is the `make()` helper — pass your key and you're ready:

```php
use Tigusigalpa\LunarCrush\LunarCrushClient;

$client = LunarCrushClient::make('YOUR_LUNARCRUSH_API_KEY');
```

Want to tune the timeout or retry policy? Build a `LunarCrushConfig` yourself. Named arguments keep it readable:

```php
use Tigusigalpa\LunarCrush\LunarCrushClient;
use Tigusigalpa\LunarCrush\LunarCrushConfig;

$config = new LunarCrushConfig(
    apiKey: 'YOUR_LUNARCRUSH_API_KEY',
    baseUrl: 'https://lunarcrush.com/api4',
    timeout: 15.0,        // seconds before an HTTP request gives up
    retryAttempts: 3,     // how many times to retry a 429
    retryDelay: 1.0,      // base backoff delay in seconds
);

$client = new LunarCrushClient($config); // Guzzle is used by default

// Prefer a different HTTP stack? Any PSR-18 client works:
$client = new LunarCrushClient($config, $yourPsr18Client);
```

You can also configure everything from environment variables with `LunarCrushConfig::fromEnv()` — handy for scripts and
CLI tools:

```php
$client = new LunarCrushClient(LunarCrushConfig::fromEnv());
```

### Laravel

The provider and facade are auto-discovered, so you only need two things: an API key and (optionally) a published config
file.

Publish the config to `config/lunarcrush.php` if you want to customise it:

```bash
php artisan vendor:publish --tag=lunarcrush-config
```

Add your credentials to `.env`:

```env
LUNARCRUSH_API_KEY=your-api-key
LUNARCRUSH_BASE_URL=https://lunarcrush.com/api4
LUNARCRUSH_TIMEOUT=15
LUNARCRUSH_RETRY_ATTEMPTS=3
LUNARCRUSH_RETRY_DELAY=1
```

Now reach for the API in whichever style you like — the facade, the container, or constructor injection:

```php
use Tigusigalpa\LunarCrush\Laravel\LunarCrushFacade as LunarCrush;
use Tigusigalpa\LunarCrush\LunarCrushClient;

// 1. Via the facade
$coins = LunarCrush::coins()->list()->sortBy('galaxy_score')->limit(10)->desc()->get();

// 2. Via the container helper
$client = app(LunarCrushClient::class);

// 3. Via dependency injection (the idiomatic Laravel way)
class MarketReportController
{
    public function __construct(private readonly LunarCrushClient $lunarCrush) {}

    public function show()
    {
        return $this->lunarCrush->topics()->topic('bitcoin')->get();
    }
}
```

The client is bound as a **singleton**, so the same instance (and its HTTP connection pool) is reused across a request
lifecycle.

### Configuration reference

| Key (`config/lunarcrush.php`) | Env variable                | Default                       | What it does                                                           |
|-------------------------------|-----------------------------|-------------------------------|------------------------------------------------------------------------|
| `api_key`                     | `LUNARCRUSH_API_KEY`        | `''`                          | Your bearer token, sent as `Authorization: Bearer <key>`.              |
| `base_url`                    | `LUNARCRUSH_BASE_URL`       | `https://lunarcrush.com/api4` | API root. Override it to point at a proxy or mock server.              |
| `timeout`                     | `LUNARCRUSH_TIMEOUT`        | `15.0`                        | Per-request timeout, in seconds.                                       |
| `retry_attempts`              | `LUNARCRUSH_RETRY_ATTEMPTS` | `3`                           | How many times a `429` is retried before giving up.                    |
| `retry_delay`                 | `LUNARCRUSH_RETRY_DELAY`    | `1.0`                         | Base backoff delay. Attempt *N* waits `retry_delay * 2^(N-1)` seconds. |

## Quick Start

Here's a whirlwind tour touching most of the SDK. Every one of these returns a typed DTO or collection:

```php
use Tigusigalpa\LunarCrush\LunarCrushClient;

$client = LunarCrushClient::make('YOUR_LUNARCRUSH_API_KEY');

// 1. Top 50 coins by Galaxy Score, highest first
$coins = $client->coins()->list()->sortBy('galaxy_score')->limit(50)->desc()->get();
foreach ($coins as $coin) {
    echo "{$coin->symbol}: {$coin->galaxyScore}\n";
}

// 2. Bitcoin's hourly social time-series over the last week
$series = $client->topics()->timeSeries('bitcoin')->interval('1w')->bucket('hour')->get();

// 3. A topic's 24h social summary
$topic = $client->topics()->topic('bitcoin')->get();

// 4. The creators driving a topic right now
$creators = $client->topics()->creators('bitcoin')->limit(20)->get();

// 5. A single coin's full detail
$btc = $client->coins()->coin('bitcoin')->get();

// 6. A stock's detail and its time-series
$stock = $client->stocks()->stock('AAPL')->get();
$stockSeries = $client->stocks()->timeSeries('AAPL')->interval('1m')->get();

// 7. A creator's profile and their recent posts
$creator = $client->creators()->creator('twitter', 'elonmusk')->get();
$posts   = $client->creators()->posts('twitter', 'elonmusk')->limit(10)->get();

// 8. An AI-generated insight about a topic
$insight = $client->ai()->topic('bitcoin')->get();
```

## Cookbook: real-world recipes

A few small, practical examples you can lift straight into a project.

### Find today's fastest-rising coins

```php
$rising = $client->coins()->list()
    ->sortBy('percent_change_24h')
    ->desc()
    ->limit(10)
    ->get();

foreach ($rising as $coin) {
    printf("%-6s %+.2f%%  (galaxy score %s)\n", $coin->symbol, $coin->percentChange24h, $coin->galaxyScore);
}
```

### Build a simple sentiment gauge for a topic

```php
$topic = $client->topics()->topic('ethereum')->get();

$mood = match (true) {
    $topic->sentiment >= 65 => 'Bullish',
    $topic->sentiment >= 45 => 'Neutral',
    default                  => 'Bearish',
};

echo "Ethereum is looking {$mood} ({$topic->sentiment}/100) with {$topic->numPosts} posts today.\n";
```

### Chart Bitcoin interactions over the past month

```php
$series = $client->coins()->timeSeries('bitcoin')
    ->interval('1m')
    ->bucket('day')
    ->get();

$points = $series->map(fn ($point) => [
    'date'         => date('Y-m-d', $point->time),
    'close'        => $point->close,
    'interactions' => $point->interactions,
]);
// $points is now ready to feed into your charting library.
```

### Discover which topics belong to a category

```php
$defiTopics = $client->categories()->topics('defi')->limit(25)->get();

echo "DeFi is currently made up of: " .
    implode(', ', $defiTopics->map(fn ($t) => $t->title)) . "\n";
```

### Use the Network enum for creators

```php
use Tigusigalpa\LunarCrush\Enums\Network;

// Pass the enum instead of a magic string — the SDK accepts both.
$creator = $client->creators()->creator(Network::YouTube, 'somechannelid')->get();
```

### Cache expensive list calls in Laravel

```php
use Illuminate\Support\Facades\Cache;
use Tigusigalpa\LunarCrush\Laravel\LunarCrushFacade as LunarCrush;

$coins = Cache::remember('lunarcrush.top-coins', now()->addMinutes(15), function () {
    return LunarCrush::coins()->list()->sortBy('market_cap')->desc()->limit(100)->get();
});
```

## Fluent Query API

Every resource exposes chainable query-builder methods, terminated by `get()` (hydrated) or `raw()` (untouched decoded
JSON):

| Method                                 | Description                                          |
|----------------------------------------|------------------------------------------------------|
| `sortBy(string $field)`                | Sort results by the given field.                     |
| `limit(int $limit)`                    | Limit the number of returned results.                |
| `page(int $page)`                      | Paginate results, where supported.                   |
| `desc()` / `asc()`                     | Sort direction.                                      |
| `bucket(string $bucket)`               | Time-series bucket size (`hour`, `day`, ...).        |
| `interval(string $interval)`           | Relative time-series window (`1w`, `1m`, `1y`, ...). |
| `start(int $timestamp)`                | Custom time-series range start (Unix timestamp).     |
| `end(int $timestamp)`                  | Custom time-series range end (Unix timestamp).       |
| `withParam(string $key, mixed $value)` | Set an arbitrary query parameter.                    |
| `withParams(array $params)`            | Merge arbitrary query parameters.                    |
| `get()`                                | Execute the request and hydrate DTOs/collections.    |
| `raw()`                                | Execute the request and return the raw decoded body. |

```php
LunarCrush::coins()->list()->sortBy('galaxy_score')->limit(50)->desc()->get();
LunarCrush::topics()->timeSeries('bitcoin')->interval('1w')->bucket('hour')->get();
```

The pattern is always the same: **pick a resource → pick an endpoint → refine with builder methods → call `get()`**.
Nothing touches the network until that final `get()` (or `raw()`), so you can build a query up gradually, pass it
around, or store it in a variable without firing a request early.

## Working with Responses

### DTOs

Single-item endpoints hand you a readonly DTO with typed, camelCased properties — so your editor autocompletes and your
static analyser is happy:

```php
$coin = $client->coins()->coin('bitcoin')->get();

$coin->symbol;            // "BTC"
$coin->price;             // 65123.42
$coin->galaxyScore;       // 74.5
$coin->percentChange24h;  // -1.83
$coin->sentiment;         // 78.0
```

Every DTO also keeps the **complete original payload** on its `raw` property. If LunarCrush ships a shiny new field
tomorrow, you can read it immediately without waiting for an SDK release:

```php
$anythingElse = $coin->raw['some_brand_new_field'] ?? null;
$asArray      = $coin->toArray(); // the raw payload, unchanged
```

### Collections

List endpoints return typed collections (`CoinCollection`, `TopicCollection`, `TimeSeriesCollection`, and friends). They
are fully iterable and array-accessible, and come with a handful of convenience helpers:

```php
$coins = $client->coins()->list()->limit(50)->get();

count($coins);          // 50           (Countable)
$coins[0];              // first CoinDto (ArrayAccess)
foreach ($coins as $c)  // ...           (IteratorAggregate)

{$coins->first();}        // first CoinDto or null
$coins->last();         // last CoinDto or null
$coins->isEmpty();      // bool
$coins->all();          // list<CoinDto>

// Filter and map without unwrapping first
$bullish = $coins->filter(fn ($c) => $c->sentiment >= 60);
$symbols = $coins->map(fn ($c) => $c->symbol);

$coins->toArray();      // list of raw payload arrays — perfect for JSON responses
```

### The `raw()` escape hatch

Prefer to skip hydration entirely and work with the decoded JSON array (for a metadata or AI-summary endpoint, say)?
Swap `get()` for `raw()`:

```php
$meta      = $client->coins()->meta('bitcoin')->raw();      // associative array
$aiSummary = $client->topics()->whatsUp('bitcoin')->raw();  // associative array
```

## Advanced Usage

### Bring your own PSR-18 client (e.g. Symfony HttpClient)

The SDK depends only on the PSR-18 interface, so you can swap Guzzle for anything compatible — useful if your app has
already standardised on another client:

```php
use Symfony\Component\HttpClient\Psr18Client;
use Tigusigalpa\LunarCrush\LunarCrushClient;
use Tigusigalpa\LunarCrush\LunarCrushConfig;

$psr18 = new Psr18Client(); // implements PSR-18 + PSR-17 factories

$client = new LunarCrushClient(
    new LunarCrushConfig(apiKey: 'YOUR_API_KEY'),
    $psr18,       // PSR-18 client
    $psr18,       // PSR-17 request factory
    $psr18,       // PSR-17 stream factory
    $psr18,       // PSR-17 URI factory
);
```

### Tune retries per environment

Rate limits differ wildly between the Hobby and Scale plans. Match your retry policy to your plan so a burst of traffic
degrades gracefully instead of throwing:

```php
$client = LunarCrushClient::make('YOUR_API_KEY', [
    'retry_attempts' => 5,
    'retry_delay'    => 2.0, // waits 2s, 4s, 8s, 16s, 32s across attempts
]);
```

### Send parameters the SDK doesn't have a named method for

The builder covers the common query parameters, but the API occasionally accepts one-off options. Reach for
`withParam()` / `withParams()`:

```php
$posts = $client->topics()->posts('bitcoin')
    ->withParam('start', strtotime('-3 days'))
    ->withParams(['end' => time(), 'limit' => 100])
    ->get();
```

## API Reference

| Resource method                         | Endpoint                                          | Description                        |
|-----------------------------------------|---------------------------------------------------|------------------------------------|
| `coins()->list()`                       | `GET /public/coins/list/v1`                       | Full coin list, cached up to 1h    |
| `coins()->listV2()`                     | `GET /public/coins/list/v2`                       | Coin list, near real-time          |
| `coins()->coin($coin)`                  | `GET /public/coins/:coin/v1`                      | Single coin detail                 |
| `coins()->meta($coin)`                  | `GET /public/coins/:coin/meta/v1`                 | Coin metadata                      |
| `coins()->timeSeries($coin)`            | `GET /public/coins/:coin/time-series/v2`          | Coin time-series                   |
| `topics()->topic($topic)`               | `GET /public/topic/:topic/v1`                     | 24h social summary for a topic     |
| `topics()->timeSeries($topic)`          | `GET /public/topic/:topic/time-series/v1`         | Historical time-series             |
| `topics()->timeSeriesV2($topic)`        | `GET /public/topic/:topic/time-series/v2`         | v2 time-series                     |
| `topics()->creators($topic)`            | `GET /public/topic/:topic/creators/v1`            | Top creators for a topic           |
| `topics()->news($topic)`                | `GET /public/topic/:topic/news/v1`                | News for a topic                   |
| `topics()->posts($topic)`               | `GET /public/topic/:topic/posts/v1`               | Posts for a topic                  |
| `topics()->whatsUp($topic)`             | `GET /public/topic/:topic/whatsup/v1`             | AI "what's up" summary             |
| `topics()->list()`                      | `GET /public/topics/list/v1`                      | List all topics                    |
| `categories()->list()`                  | `GET /public/categories/list/v1`                  | List all categories                |
| `categories()->category($category)`     | `GET /public/category/:category/v1`               | Category summary                   |
| `categories()->creators($category)`     | `GET /public/category/:category/creators/v1`      | Top creators for a category        |
| `categories()->news($category)`         | `GET /public/category/:category/news/v1`          | News for a category                |
| `categories()->posts($category)`        | `GET /public/category/:category/posts/v1`         | Posts for a category               |
| `categories()->timeSeries($category)`   | `GET /public/category/:category/time-series/v1`   | Category time-series               |
| `categories()->topics($category)`       | `GET /public/category/:category/topics/v1`        | Topics within a category           |
| `creators()->creator($network, $id)`    | `GET /public/creator/:network/:id/v1`             | Creator detail                     |
| `creators()->posts($network, $id)`      | `GET /public/creator/:network/:id/posts/v1`       | Creator posts                      |
| `creators()->timeSeries($network, $id)` | `GET /public/creator/:network/:id/time-series/v1` | Creator time-series                |
| `creators()->list()`                    | `GET /public/creators/list/v1`                    | List top creators                  |
| `posts()->list()`                       | `GET /public/posts/v1`                            | List posts                         |
| `posts()->timeSeries()`                 | `GET /public/posts/time-series/v1`                | Aggregate post time-series         |
| `stocks()->list()`                      | `GET /public/stocks/list/v1`                      | List stocks                        |
| `stocks()->listV2()`                    | `GET /public/stocks/list/v2`                      | List stocks (v2)                   |
| `stocks()->stock($stock)`               | `GET /public/stocks/:stock/v1`                    | Single stock detail                |
| `stocks()->timeSeries($stock)`          | `GET /public/stocks/:stock/time-series/v2`        | Stock time-series                  |
| `searches()->create($params)`           | `GET /public/searches/create`                     | Create a custom search aggregation |
| `searches()->list()`                    | `GET /public/searches/list`                       | List existing searches             |
| `searches()->search($term)`             | `GET /public/searches/search`                     | Search within aggregations         |
| `searches()->show($slug)`               | `GET /public/searches/:slug`                      | Get aggregation summary            |
| `searches()->update($slug, $params)`    | `GET /public/searches/:slug/update`               | Update an aggregation              |
| `searches()->delete($slug)`             | `GET /public/searches/:slug/delete`               | Delete an aggregation              |
| `ai()->topic($topic)`                   | `GET /public/ai/topic/:topic`                     | AI-generated topic insight         |
| `ai()->creator($network, $id)`          | `GET /public/ai/creator/:network/:id`             | AI-generated creator insight       |
| `system()->changes()`                   | `GET /public/system/changes`                      | Historical data change log         |

### Rate limits by plan

| Plan       | Requests / minute | Requests / day |
|------------|-------------------|----------------|
| Hobby      | 4                 | 100            |
| Individual | 10                | 2,000          |
| Builder    | 100               | 20,000         |
| Scale      | 500               | 100,000        |

## Error Handling

All exceptions extend `Tigusigalpa\LunarCrush\Exceptions\LunarCrushException`:

```
LunarCrushException (base)
├── ApiException          — non-2xx response with parsed error message
├── RateLimitException    — HTTP 429 (after retries are exhausted)
├── UnauthorizedException — HTTP 401
└── NotFoundException     — HTTP 404
```

```php
use Tigusigalpa\LunarCrush\Exceptions\NotFoundException;
use Tigusigalpa\LunarCrush\Exceptions\RateLimitException;
use Tigusigalpa\LunarCrush\Exceptions\UnauthorizedException;
use Tigusigalpa\LunarCrush\Exceptions\LunarCrushException;

try {
    $coin = $client->coins()->coin('bitcoin')->get();
} catch (RateLimitException $e) {
    // Retries (default 3, exponential backoff starting at 1s) were exhausted.
} catch (UnauthorizedException $e) {
    // Invalid or missing API key.
} catch (NotFoundException $e) {
    // Coin does not exist.
} catch (LunarCrushException $e) {
    // Any other SDK error.
}
```

**Good to know:**

- `RateLimitException`, `UnauthorizedException`, and `NotFoundException` all extend `ApiException`, which in turn
  extends `LunarCrushException`. Catch as broadly or narrowly as you like.
- Every exception carries context: `$e->statusCode` (the HTTP status) and `$e->responseBody` (the decoded error
  payload). `RateLimitException` additionally exposes `$e->retryAfter` when the server sent that header.
- A `RateLimitException` is only thrown **after** the automatic retries are exhausted — by the time you catch it, the
  SDK has already tried its best.

Retry behaviour is configurable via `LunarCrushConfig::$retryAttempts` and `LunarCrushConfig::$retryDelay` (or
`retry_attempts` / `retry_delay` in the Laravel config file). The delay for retry attempt *N* is `retry_delay * 2^(N-1)`
seconds — unless the API returns a `Retry-After` header, which always takes precedence.

## Testing

The package ships with a full PHPUnit 10 suite:

```bash
composer install
vendor/bin/phpunit
```

Every test mocks its HTTP responses with Guzzle's `MockHandler`, so **no real API calls are made** and the suite runs
offline in well under a second (apart from bootstrapping). It covers successful responses, DTO/collection hydration, the
rate-limit retry loop, exception mapping, and — via `orchestra/testbench` — the Laravel service provider and facade.

## Testing Your Own App

Because the client accepts any PSR-18 implementation, faking LunarCrush in *your* test suite is easy. Hand it a Guzzle
`MockHandler` and assert against the typed results — no network required:

```php
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Tigusigalpa\LunarCrush\LunarCrushClient;
use Tigusigalpa\LunarCrush\LunarCrushConfig;

$mock  = new MockHandler([
    new Response(200, [], json_encode([
        'data' => [['id' => 1, 'symbol' => 'BTC', 'name' => 'Bitcoin', 'galaxy_score' => 75]],
    ])),
]);
$guzzle = new GuzzleClient(['handler' => HandlerStack::create($mock)]);

$client = new LunarCrushClient(new LunarCrushConfig(apiKey: 'test'), $guzzle);

$coins = $client->coins()->list()->get();
// assert $coins->first()->symbol === 'BTC';
```

In a Laravel app, bind your mocked client into the container in a test's `setUp()` and the facade will use it
automatically.

## FAQ

**Do I need Laravel to use this?**
No. The core is completely framework-agnostic — the Laravel provider and facade are an optional convenience layer.

**Which HTTP client does it use?**
Guzzle `^7.4` out of the box. You can inject any PSR-18 client (Symfony HttpClient, HTTPlug, …) through the constructor.

**What happens when I hit a rate limit?**
The SDK automatically retries with exponential backoff (respecting a `Retry-After` header if present). Only once the
configured attempts are exhausted does it throw a `RateLimitException`.

**A field I need isn't a typed property on the DTO — now what?**
Every DTO keeps the full original payload on `->raw` (and `->toArray()`). Nothing is ever hidden from you.

**Is my API key safe?**
Your key lives in config/`.env` and is only ever sent as a `Bearer` header over HTTPS. Never hard-code it in committed
source.

## Roadmap

- Optional built-in response caching layer (PSR-6 / PSR-16).
- First-class async / concurrent request support.
- Additional typed DTOs for metadata and AI-insight endpoints.
- Laravel Artisan commands for quick CLI lookups.

Have an idea? [Open an issue](https://github.com/tigusigalpa/lunarcrush-php/issues) — suggestions are genuinely welcome.

## Contributing

Contributions of every size are welcome and appreciated — from fixing a typo to adding a whole endpoint. To get started:

1. Fork the repository.
2. Create a feature branch (`git checkout -b feature/my-improvement`).
3. Add or update tests for your change.
4. Make sure `vendor/bin/phpunit` passes.
5. Open a pull request describing what and why.

Found a bug or have a question? [Open an issue](https://github.com/tigusigalpa/lunarcrush-php/issues) — no template
gymnastics required.

## License

Released under the [MIT License](LICENSE). Use it freely in personal and commercial projects alike.

## Credits

- Built and maintained
  by [Igor Sazonov](https://github.com/tigusigalpa) — [sovletig@gmail.com](mailto:sovletig@gmail.com).
- Powered by the [LunarCrush API](https://lunarcrush.com/developers/api). This is an independent, community-built SDK
  and is not officially affiliated with LunarCrush.

---

If this package saves you some time, consider giving it a ⭐ on [GitHub](https://github.com/tigusigalpa/lunarcrush-php) —
it genuinely helps others find it.
