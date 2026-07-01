<?php

declare(strict_types=1);

namespace Tigusigalpa\LunarCrush\Dto;

/**
 * Represents a single stock/equity as returned by the LunarCrush Stocks endpoints.
 */
final class StockDto
{
    /**
     * @param int|string $id               LunarCrush internal stock identifier.
     * @param string     $symbol           Ticker symbol, e.g. `AAPL`.
     * @param string     $name             Company name.
     * @param float|null $price            Current price in USD.
     * @param float|null $volume24h        Trading volume over the last 24 hours.
     * @param float|null $percentChange24h Percent price change over the last 24 hours.
     * @param float|null $marketCap        Market capitalization in USD.
     * @param int|null   $marketCapRank    Market capitalization rank.
     * @param int|null   $interactions24h  Total social interactions over the last 24 hours.
     * @param int|null   $socialVolume24h  Number of social posts over the last 24 hours.
     * @param float|null $galaxyScore      LunarCrush proprietary Galaxy Score (0-100).
     * @param int|null   $altRank          LunarCrush proprietary AltRank.
     * @param float|null $sentiment        Aggregate sentiment score (0-100).
     * @param array<string, mixed> $raw    Original, unmapped API payload for forward compatibility.
     */
    public function __construct(
        public readonly int|string $id,
        public readonly string $symbol,
        public readonly string $name,
        public readonly ?float $price = null,
        public readonly ?float $volume24h = null,
        public readonly ?float $percentChange24h = null,
        public readonly ?float $marketCap = null,
        public readonly ?int $marketCapRank = null,
        public readonly ?int $interactions24h = null,
        public readonly ?int $socialVolume24h = null,
        public readonly ?float $galaxyScore = null,
        public readonly ?int $altRank = null,
        public readonly ?float $sentiment = null,
        public readonly array $raw = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? '',
            symbol: (string) ($data['symbol'] ?? ''),
            name: (string) ($data['name'] ?? ''),
            price: isset($data['price']) ? (float) $data['price'] : null,
            volume24h: isset($data['volume_24h']) ? (float) $data['volume_24h'] : null,
            percentChange24h: isset($data['percent_change_24h']) ? (float) $data['percent_change_24h'] : null,
            marketCap: isset($data['market_cap']) ? (float) $data['market_cap'] : null,
            marketCapRank: isset($data['market_cap_rank']) ? (int) $data['market_cap_rank'] : null,
            interactions24h: isset($data['interactions_24h']) ? (int) $data['interactions_24h'] : null,
            socialVolume24h: isset($data['social_volume_24h']) ? (int) $data['social_volume_24h'] : null,
            galaxyScore: isset($data['galaxy_score']) ? (float) $data['galaxy_score'] : null,
            altRank: isset($data['alt_rank']) ? (int) $data['alt_rank'] : null,
            sentiment: isset($data['sentiment']) ? (float) $data['sentiment'] : null,
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
