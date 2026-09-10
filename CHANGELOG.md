# Changelog

All notable changes to `tigusigalpa/lunarcrush-php` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

- Laravel 12 and 13 support: widened `illuminate/support` to `^10.0 || ^11.0 || ^12.0 || ^13.0`
  and `orchestra/testbench` to `^8.0 || ^9.0 || ^10.0 || ^11.0`.

### Changed

- Broadened the `phpunit/phpunit` dev requirement to `^10.5 || ^11.0 || ^12.0` so the suite can run
  against the PHPUnit versions used by Laravel 12/13 test tooling.

### Fixed

- URL-encode caller-supplied path segments across all resource groups, so reserved characters cannot alter
  the LunarCrush endpoint being requested.
- Preserve explicit `0` values for retry settings loaded from environment variables.
- Require Guzzle 7.15.5 or later to exclude known vulnerabilities in older 7.x releases.

## [1.0.0] - 2025-01-01

### Added

- Initial release of the LunarCrush API v4 PHP SDK.
- Framework-agnostic `LunarCrushClient` with PSR-18 HTTP client support (Guzzle by default).
- Fluent, chainable query builder shared across all resource groups.
- Resource groups: `CoinsResource`, `TopicsResource`, `CategoriesResource`, `CreatorsResource`,
  `PostsResource`, `StocksResource`, `SearchesResource`, `SystemResource`, `AiResource`.
- Strongly typed, readonly DTOs: `CoinDto`, `TopicDto`, `StockDto`, `CreatorDto`, `PostDto`,
  `TimeSeriesPointDto`, `SearchDto`, `CategoryDto`.
- Typed collections: `CoinCollection`, `TopicCollection`, `StockCollection`, `CreatorCollection`,
  `PostCollection`, `CategoryCollection`, `SearchCollection`, `TimeSeriesCollection`.
- Exception hierarchy: `LunarCrushException`, `ApiException`, `RateLimitException`,
  `UnauthorizedException`, `NotFoundException`.
- Automatic exponential-backoff retry logic for HTTP 429 rate-limit responses.
- Laravel 10+/11+ integration: `LunarCrushServiceProvider`, `LunarCrushFacade`, and a publishable
  `config/lunarcrush.php` configuration file.
- PHPUnit 10 test suite covering successful responses, retry logic, exception mapping, DTO
  hydration, and Laravel service provider/facade integration.
