<?php

declare(strict_types=1);

namespace Tigusigalpa\LunarCrush\Dto;

/**
 * Represents a social category as returned by the LunarCrush Categories endpoints.
 */
final class CategoryDto
{
    /**
     * @param string     $category         Category slug, e.g. `defi`.
     * @param string     $title            Human-readable category title.
     * @param int|null   $categoryRank     Rank of the category by social activity.
     * @param int|null   $numPosts         Number of posts over the last 24 hours.
     * @param int|null   $interactions24h  Total social interactions over the last 24 hours.
     * @param float|null $socialDominance  Share of total social volume across all categories.
     * @param list<string> $topics         Slugs of topics included in the category.
     * @param array<string, mixed> $raw    Original, unmapped API payload for forward compatibility.
     */
    public function __construct(
        public readonly string $category,
        public readonly string $title,
        public readonly ?int $categoryRank = null,
        public readonly ?int $numPosts = null,
        public readonly ?int $interactions24h = null,
        public readonly ?float $socialDominance = null,
        public readonly array $topics = [],
        public readonly array $raw = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            category: (string) ($data['category'] ?? $data['slug'] ?? ''),
            title: (string) ($data['title'] ?? $data['name'] ?? ''),
            categoryRank: isset($data['category_rank']) ? (int) $data['category_rank'] : null,
            numPosts: isset($data['num_posts']) ? (int) $data['num_posts'] : null,
            interactions24h: isset($data['interactions_24h']) ? (int) $data['interactions_24h'] : null,
            socialDominance: isset($data['social_dominance']) ? (float) $data['social_dominance'] : null,
            topics: array_values((array) ($data['topics'] ?? [])),
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
