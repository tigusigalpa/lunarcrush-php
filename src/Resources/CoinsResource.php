<?php

declare(strict_types=1);

namespace Tigusigalpa\LunarCrush\Resources;

use Tigusigalpa\LunarCrush\Collections\CoinCollection;
use Tigusigalpa\LunarCrush\Collections\TimeSeriesCollection;
use Tigusigalpa\LunarCrush\Dto\CoinDto;
use Tigusigalpa\LunarCrush\Dto\TimeSeriesPointDto;

/**
 * Fluent builder for the LunarCrush Coins endpoint group.
 *
 * @example
 * ```php
 * $coins = $client->coins()->list()->sortBy('galaxy_score')->limit(50)->desc()->get();
 * ```
 */
final class CoinsResource extends AbstractResource
{
    /**
     * List all coins (cached up to 1 hour). `GET /public/coins/list/v1`.
     *
     * @return CoinCollection|CoinDto[]
     */
    public function list(): static
    {
        return $this->reset('/public/coins/list/v1')->asCollection(CoinDto::class, CoinCollection::class);
    }

    /**
     * List all coins, near real-time. `GET /public/coins/list/v2`.
     */
    public function listV2(): static
    {
        return $this->reset('/public/coins/list/v2')->asCollection(CoinDto::class, CoinCollection::class);
    }

    /**
     * Fetch a single coin's detail. `GET /public/coins/:coin/v1`.
     *
     * @param string $coin Coin symbol or LunarCrush coin ID, e.g. `bitcoin` or `BTC`.
     */
    public function coin(string $coin): static
    {
        return $this->reset('/public/coins/' . $this->encodePathSegment($coin) . '/v1')->asItem(CoinDto::class);
    }

    /**
     * Fetch metadata for a single coin (description, links, etc.). `GET /public/coins/:coin/meta/v1`.
     *
     * @param string $coin Coin symbol or LunarCrush coin ID.
     */
    public function meta(string $coin): static
    {
        return $this->reset('/public/coins/' . $this->encodePathSegment($coin) . '/meta/v1');
    }

    /**
     * Fetch time-series history for a coin. `GET /public/coins/:coin/time-series/v2`.
     *
     * @param string $coin Coin symbol or LunarCrush coin ID.
     *
     * @return TimeSeriesCollection|TimeSeriesPointDto[]
     */
    public function timeSeries(string $coin): static
    {
        return $this->reset('/public/coins/' . $this->encodePathSegment($coin) . '/time-series/v2')
            ->asCollection(TimeSeriesPointDto::class, TimeSeriesCollection::class);
    }
}
