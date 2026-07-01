<?php

declare(strict_types=1);

namespace Tigusigalpa\LunarCrush\Dto;

/**
 * Represents a single data point of a LunarCrush time-series response,
 * used across Coins, Stocks, Topics, and Categories time-series endpoints.
 */
final class TimeSeriesPointDto
{
    /**
     * @param int        $time               Unix timestamp of the data point (bucket start).
     * @param float|null $open               Opening price for the bucket.
     * @param float|null $close              Closing price for the bucket.
     * @param float|null $high               Highest price for the bucket.
     * @param float|null $low                Lowest price for the bucket.
     * @param float|null $volume             Trading volume for the bucket.
     * @param float|null $marketCap          Market capitalization at the end of the bucket.
     * @param int|null   $interactions       Total social interactions for the bucket.
     * @param int|null   $contributorsActive Number of unique active contributors for the bucket.
     * @param int|null   $contributorsCreated Number of new contributors for the bucket.
     * @param int|null   $postsActive        Number of active posts for the bucket.
     * @param int|null   $postsCreated       Number of new posts created for the bucket.
     * @param float|null $sentiment          Aggregate sentiment score (0-100) for the bucket.
     * @param float|null $spam               Spam score for the bucket.
     * @param float|null $galaxyScore        LunarCrush proprietary Galaxy Score (0-100) for the bucket.
     * @param int|null   $altRank            LunarCrush proprietary AltRank for the bucket.
     * @param float|null $socialDominance    Share of total social volume for the bucket.
     * @param array<string, mixed> $raw      Original, unmapped API payload for forward compatibility.
     */
    public function __construct(
        public readonly int $time,
        public readonly ?float $open = null,
        public readonly ?float $close = null,
        public readonly ?float $high = null,
        public readonly ?float $low = null,
        public readonly ?float $volume = null,
        public readonly ?float $marketCap = null,
        public readonly ?int $interactions = null,
        public readonly ?int $contributorsActive = null,
        public readonly ?int $contributorsCreated = null,
        public readonly ?int $postsActive = null,
        public readonly ?int $postsCreated = null,
        public readonly ?float $sentiment = null,
        public readonly ?float $spam = null,
        public readonly ?float $galaxyScore = null,
        public readonly ?int $altRank = null,
        public readonly ?float $socialDominance = null,
        public readonly array $raw = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            time: (int) ($data['time'] ?? 0),
            open: isset($data['open']) ? (float) $data['open'] : null,
            close: isset($data['close']) ? (float) $data['close'] : null,
            high: isset($data['high']) ? (float) $data['high'] : null,
            low: isset($data['low']) ? (float) $data['low'] : null,
            volume: isset($data['volume']) ? (float) $data['volume'] : null,
            marketCap: isset($data['market_cap']) ? (float) $data['market_cap'] : null,
            interactions: isset($data['interactions']) ? (int) $data['interactions'] : null,
            contributorsActive: isset($data['contributors_active']) ? (int) $data['contributors_active'] : null,
            contributorsCreated: isset($data['contributors_created']) ? (int) $data['contributors_created'] : null,
            postsActive: isset($data['posts_active']) ? (int) $data['posts_active'] : null,
            postsCreated: isset($data['posts_created']) ? (int) $data['posts_created'] : null,
            sentiment: isset($data['sentiment']) ? (float) $data['sentiment'] : null,
            spam: isset($data['spam']) ? (float) $data['spam'] : null,
            galaxyScore: isset($data['galaxy_score']) ? (float) $data['galaxy_score'] : null,
            altRank: isset($data['alt_rank']) ? (int) $data['alt_rank'] : null,
            socialDominance: isset($data['social_dominance']) ? (float) $data['social_dominance'] : null,
            raw: $data,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->raw;
    }
}
