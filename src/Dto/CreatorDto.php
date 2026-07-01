<?php

declare(strict_types=1);

namespace Tigusigalpa\LunarCrush\Dto;

/**
 * Represents a social media creator/influencer as returned by the LunarCrush Creators endpoints.
 */
final class CreatorDto
{
    /**
     * @param string     $network          Social network, e.g. `twitter`, `youtube`.
     * @param string     $id               Creator identifier on the given network.
     * @param string     $name             Creator handle/username.
     * @param string     $displayName      Creator display name.
     * @param string|null $avatar          URL to the creator's avatar image.
     * @param int|null   $followers        Number of followers.
     * @param int|null   $rank             Creator rank by social influence.
     * @param int|null   $interactions24h  Total social interactions over the last 24 hours.
     * @param int|null   $postsActive      Number of active posts.
     * @param int|null   $posts24h         Number of posts over the last 24 hours.
     * @param array<string, mixed> $raw    Original, unmapped API payload for forward compatibility.
     */
    public function __construct(
        public readonly string $network,
        public readonly string $id,
        public readonly string $name,
        public readonly string $displayName = '',
        public readonly ?string $avatar = null,
        public readonly ?int $followers = null,
        public readonly ?int $rank = null,
        public readonly ?int $interactions24h = null,
        public readonly ?int $postsActive = null,
        public readonly ?int $posts24h = null,
        public readonly array $raw = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            network: (string) ($data['creator_network'] ?? $data['network'] ?? ''),
            id: (string) ($data['creator_id'] ?? $data['id'] ?? ''),
            name: (string) ($data['creator_name'] ?? $data['name'] ?? ''),
            displayName: (string) ($data['creator_display_name'] ?? $data['display_name'] ?? ''),
            avatar: $data['creator_avatar'] ?? $data['avatar'] ?? null,
            followers: isset($data['creator_followers']) ? (int) $data['creator_followers'] : (isset($data['followers']) ? (int) $data['followers'] : null),
            rank: isset($data['creator_rank']) ? (int) $data['creator_rank'] : (isset($data['rank']) ? (int) $data['rank'] : null),
            interactions24h: isset($data['interactions_24h']) ? (int) $data['interactions_24h'] : null,
            postsActive: isset($data['posts_active']) ? (int) $data['posts_active'] : null,
            posts24h: isset($data['posts_24h']) ? (int) $data['posts_24h'] : null,
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
