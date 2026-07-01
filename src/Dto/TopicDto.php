<?php

declare(strict_types=1);

namespace Tigusigalpa\LunarCrush\Dto;

/**
 * Represents a social topic as returned by the LunarCrush Topics endpoints.
 */
final class TopicDto
{
    /**
     * @param string        $topic              Topic slug, e.g. `bitcoin`.
     * @param string        $title              Human-readable topic title.
     * @param int|null      $topicRank          Rank of the topic by social activity.
     * @param int|null      $numContributors    Number of unique contributors over the last 24 hours.
     * @param int|null      $numPosts           Number of posts over the last 24 hours.
     * @param int|null      $interactions24h    Total social interactions over the last 24 hours.
     * @param float|null    $socialDominance    Share of total social volume across all topics.
     * @param float|null    $sentiment          Aggregate sentiment score (0-100).
     * @param list<string>  $categories         Category slugs associated with the topic.
     * @param list<string>  $relatedTopics      Slugs of related topics.
     * @param array<string, mixed> $trend       Trend metadata (e.g. up/down/flat), if provided by the API.
     * @param array<string, mixed> $raw         Original, unmapped API payload for forward compatibility.
     */
    public function __construct(
        public readonly string $topic,
        public readonly string $title,
        public readonly ?int $topicRank = null,
        public readonly ?int $numContributors = null,
        public readonly ?int $numPosts = null,
        public readonly ?int $interactions24h = null,
        public readonly ?float $socialDominance = null,
        public readonly ?float $sentiment = null,
        public readonly array $categories = [],
        public readonly array $relatedTopics = [],
        public readonly array $trend = [],
        public readonly array $raw = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            topic: (string) ($data['topic'] ?? $data['slug'] ?? ''),
            title: (string) ($data['title'] ?? $data['name'] ?? ''),
            topicRank: isset($data['topic_rank']) ? (int) $data['topic_rank'] : null,
            numContributors: isset($data['num_contributors']) ? (int) $data['num_contributors'] : null,
            numPosts: isset($data['num_posts']) ? (int) $data['num_posts'] : null,
            interactions24h: isset($data['interactions_24h']) ? (int) $data['interactions_24h'] : null,
            socialDominance: isset($data['social_dominance']) ? (float) $data['social_dominance'] : null,
            sentiment: isset($data['sentiment']) ? (float) $data['sentiment'] : null,
            categories: array_values((array) ($data['categories'] ?? [])),
            relatedTopics: array_values((array) ($data['related_topics'] ?? [])),
            trend: (array) ($data['trend'] ?? []),
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
