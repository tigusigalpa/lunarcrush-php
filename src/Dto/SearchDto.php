<?php

declare(strict_types=1);

namespace Tigusigalpa\LunarCrush\Dto;

/**
 * Represents a custom search aggregation as returned by the LunarCrush Searches endpoints.
 */
final class SearchDto
{
    /**
     * @param string                $slug        Unique slug identifying the saved search.
     * @param string                $name        Human-readable name of the search.
     * @param list<string>          $terms       Search terms/keywords included in the aggregation.
     * @param int|null              $createdAt   Unix timestamp when the search was created.
     * @param int|null              $updatedAt   Unix timestamp when the search was last updated.
     * @param array<string, mixed> $config       Full aggregation configuration payload.
     * @param array<string, mixed> $raw          Original, unmapped API payload for forward compatibility.
     */
    public function __construct(
        public readonly string $slug,
        public readonly string $name = '',
        public readonly array $terms = [],
        public readonly ?int $createdAt = null,
        public readonly ?int $updatedAt = null,
        public readonly array $config = [],
        public readonly array $raw = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            slug: (string) ($data['slug'] ?? $data['id'] ?? ''),
            name: (string) ($data['name'] ?? ''),
            terms: array_values((array) ($data['terms'] ?? $data['keywords'] ?? [])),
            createdAt: isset($data['created']) ? (int) $data['created'] : null,
            updatedAt: isset($data['updated']) ? (int) $data['updated'] : null,
            config: (array) ($data['config'] ?? []),
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
