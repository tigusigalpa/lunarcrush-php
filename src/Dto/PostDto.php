<?php

declare(strict_types=1);

namespace Tigusigalpa\LunarCrush\Dto;

/**
 * Represents a single social post or news item as returned by the LunarCrush
 * Posts, Topics, and Categories endpoints.
 */
final class PostDto
{
    /**
     * @param string      $id               Post identifier.
     * @param string      $type             Post type, e.g. `tweet`, `reddit-post`, `news`.
     * @param string|null $title            Post title (mainly populated for news items).
     * @param string|null $link             URL to the original post.
     * @param int|null    $createdAt        Unix timestamp when the post was created.
     * @param float|null  $sentiment        Sentiment score of the post (0-100).
     * @param string|null $creatorId        Identifier of the creator who authored the post.
     * @param string|null $creatorName      Handle/username of the creator.
     * @param string|null $creatorDisplayName Display name of the creator.
     * @param string|null $creatorAvatar    URL to the creator's avatar image.
     * @param int|null    $creatorFollowers Number of followers of the creator.
     * @param int|null    $interactions24h  Interactions accrued over the last 24 hours.
     * @param int|null    $interactionsTotal Total interactions accrued since the post was created.
     * @param array<string, mixed> $raw     Original, unmapped API payload for forward compatibility.
     */
    public function __construct(
        public readonly string $id,
        public readonly string $type,
        public readonly ?string $title = null,
        public readonly ?string $link = null,
        public readonly ?int $createdAt = null,
        public readonly ?float $sentiment = null,
        public readonly ?string $creatorId = null,
        public readonly ?string $creatorName = null,
        public readonly ?string $creatorDisplayName = null,
        public readonly ?string $creatorAvatar = null,
        public readonly ?int $creatorFollowers = null,
        public readonly ?int $interactions24h = null,
        public readonly ?int $interactionsTotal = null,
        public readonly array $raw = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (string) ($data['id'] ?? $data['post_id'] ?? ''),
            type: (string) ($data['post_type'] ?? $data['type'] ?? ''),
            title: $data['post_title'] ?? $data['title'] ?? null,
            link: $data['post_link'] ?? $data['url'] ?? null,
            createdAt: isset($data['post_created']) ? (int) $data['post_created'] : (isset($data['created']) ? (int) $data['created'] : null),
            sentiment: isset($data['post_sentiment']) ? (float) $data['post_sentiment'] : (isset($data['sentiment']) ? (float) $data['sentiment'] : null),
            creatorId: $data['creator_id'] ?? null,
            creatorName: $data['creator_name'] ?? null,
            creatorDisplayName: $data['creator_display_name'] ?? null,
            creatorAvatar: $data['creator_avatar'] ?? null,
            creatorFollowers: isset($data['creator_followers']) ? (int) $data['creator_followers'] : null,
            interactions24h: isset($data['interactions_24h']) ? (int) $data['interactions_24h'] : null,
            interactionsTotal: isset($data['interactions_total']) ? (int) $data['interactions_total'] : null,
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
