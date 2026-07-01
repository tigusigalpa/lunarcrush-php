<?php

declare(strict_types=1);

namespace Tigusigalpa\LunarCrush\Dto;

/**
 * Represents a single cryptocurrency as returned by the LunarCrush Coins endpoints.
 */
final class CoinDto
{
    /**
     * @param int|string  $id                  LunarCrush internal coin identifier.
     * @param string      $symbol              Ticker symbol, e.g. `BTC`.
     * @param string      $name                Full coin name, e.g. `Bitcoin`.
     * @param float|null  $price               Current price in USD.
     * @param float|null  $priceBtc            Current price denominated in BTC.
     * @param float|null  $volume24h           Trading volume over the last 24 hours (USD).
     * @param float|null  $volatility          Price volatility metric.
     * @param float|null  $circulatingSupply   Circulating supply.
     * @param float|null  $maxSupply           Maximum supply, if capped.
     * @param float|null  $percentChange1h     Percent price change over the last hour.
     * @param float|null  $percentChange24h    Percent price change over the last 24 hours.
     * @param float|null  $percentChange7d     Percent price change over the last 7 days.
     * @param float|null  $percentChange30d    Percent price change over the last 30 days.
     * @param float|null  $marketCap           Market capitalization in USD.
     * @param int|null    $marketCapRank       Market capitalization rank.
     * @param int|null    $interactions24h     Total social interactions over the last 24 hours.
     * @param int|null    $socialVolume24h     Number of social posts over the last 24 hours.
     * @param float|null  $socialDominance     Share of total social volume across all assets.
     * @param float|null  $marketDominance     Share of total crypto market capitalization.
     * @param float|null  $galaxyScore         LunarCrush proprietary Galaxy Score (0-100).
     * @param int|null    $altRank             LunarCrush proprietary AltRank.
     * @param float|null  $sentiment           Aggregate sentiment score (0-100).
     * @param list<string> $categories        Category slugs the coin belongs to.
     * @param list<string> $blockchains       Blockchain networks the coin is deployed on.
     * @param array<string, mixed> $raw       Original, unmapped API payload for forward compatibility.
     */
    public function __construct(
        public readonly int|string $id,
        public readonly string $symbol,
        public readonly string $name,
        public readonly ?float $price = null,
        public readonly ?float $priceBtc = null,
        public readonly ?float $volume24h = null,
        public readonly ?float $volatility = null,
        public readonly ?float $circulatingSupply = null,
        public readonly ?float $maxSupply = null,
        public readonly ?float $percentChange1h = null,
        public readonly ?float $percentChange24h = null,
        public readonly ?float $percentChange7d = null,
        public readonly ?float $percentChange30d = null,
        public readonly ?float $marketCap = null,
        public readonly ?int $marketCapRank = null,
        public readonly ?int $interactions24h = null,
        public readonly ?int $socialVolume24h = null,
        public readonly ?float $socialDominance = null,
        public readonly ?float $marketDominance = null,
        public readonly ?float $galaxyScore = null,
        public readonly ?int $altRank = null,
        public readonly ?float $sentiment = null,
        public readonly array $categories = [],
        public readonly array $blockchains = [],
        public readonly array $raw = [],
    ) {
    }

    /**
     * Hydrate a DTO instance from a raw LunarCrush API array payload.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? '',
            symbol: (string) ($data['symbol'] ?? ''),
            name: (string) ($data['name'] ?? ''),
            price: isset($data['price']) ? (float) $data['price'] : null,
            priceBtc: isset($data['price_btc']) ? (float) $data['price_btc'] : null,
            volume24h: isset($data['volume_24h']) ? (float) $data['volume_24h'] : null,
            volatility: isset($data['volatility']) ? (float) $data['volatility'] : null,
            circulatingSupply: isset($data['circulating_supply']) ? (float) $data['circulating_supply'] : null,
            maxSupply: isset($data['max_supply']) ? (float) $data['max_supply'] : null,
            percentChange1h: isset($data['percent_change_1h']) ? (float) $data['percent_change_1h'] : null,
            percentChange24h: isset($data['percent_change_24h']) ? (float) $data['percent_change_24h'] : null,
            percentChange7d: isset($data['percent_change_7d']) ? (float) $data['percent_change_7d'] : null,
            percentChange30d: isset($data['percent_change_30d']) ? (float) $data['percent_change_30d'] : null,
            marketCap: isset($data['market_cap']) ? (float) $data['market_cap'] : null,
            marketCapRank: isset($data['market_cap_rank']) ? (int) $data['market_cap_rank'] : null,
            interactions24h: isset($data['interactions_24h']) ? (int) $data['interactions_24h'] : null,
            socialVolume24h: isset($data['social_volume_24h']) ? (int) $data['social_volume_24h'] : null,
            socialDominance: isset($data['social_dominance']) ? (float) $data['social_dominance'] : null,
            marketDominance: isset($data['market_dominance']) ? (float) $data['market_dominance'] : null,
            galaxyScore: isset($data['galaxy_score']) ? (float) $data['galaxy_score'] : null,
            altRank: isset($data['alt_rank']) ? (int) $data['alt_rank'] : null,
            sentiment: isset($data['sentiment']) ? (float) $data['sentiment'] : null,
            categories: array_values((array) ($data['categories'] ?? [])),
            blockchains: array_values((array) ($data['blockchains'] ?? [])),
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
