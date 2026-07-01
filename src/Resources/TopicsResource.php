<?php

declare(strict_types=1);

namespace Tigusigalpa\LunarCrush\Resources;

use Tigusigalpa\LunarCrush\Collections\CreatorCollection;
use Tigusigalpa\LunarCrush\Collections\PostCollection;
use Tigusigalpa\LunarCrush\Collections\TimeSeriesCollection;
use Tigusigalpa\LunarCrush\Collections\TopicCollection;
use Tigusigalpa\LunarCrush\Dto\CreatorDto;
use Tigusigalpa\LunarCrush\Dto\PostDto;
use Tigusigalpa\LunarCrush\Dto\TimeSeriesPointDto;
use Tigusigalpa\LunarCrush\Dto\TopicDto;

/**
 * Fluent builder for the LunarCrush Topics endpoint group.
 *
 * @example
 * ```php
 * $series = $client->topics()->timeSeries('bitcoin')->interval('1w')->bucket('hour')->get();
 * ```
 */
final class TopicsResource extends AbstractResource
{
    /**
     * List all available topics. `GET /public/topics/list/v1`.
     *
     * @return TopicCollection|TopicDto[]
     */
    public function list(): static
    {
        return $this->reset('/public/topics/list/v1')->asCollection(TopicDto::class, TopicCollection::class);
    }

    /**
     * Fetch the 24h social summary for a topic. `GET /public/topic/:topic/v1`.
     */
    public function topic(string $topic): static
    {
        return $this->reset("/public/topic/{$topic}/v1")->asItem(TopicDto::class);
    }

    /**
     * Fetch historical time-series (v1) for a topic. `GET /public/topic/:topic/time-series/v1`.
     *
     * @return TimeSeriesCollection|TimeSeriesPointDto[]
     */
    public function timeSeries(string $topic): static
    {
        return $this->reset("/public/topic/{$topic}/time-series/v1")
            ->asCollection(TimeSeriesPointDto::class, TimeSeriesCollection::class);
    }

    /**
     * Fetch historical time-series (v2) for a topic. `GET /public/topic/:topic/time-series/v2`.
     *
     * @return TimeSeriesCollection|TimeSeriesPointDto[]
     */
    public function timeSeriesV2(string $topic): static
    {
        return $this->reset("/public/topic/{$topic}/time-series/v2")
            ->asCollection(TimeSeriesPointDto::class, TimeSeriesCollection::class);
    }

    /**
     * Fetch the top creators for a topic. `GET /public/topic/:topic/creators/v1`.
     *
     * @return CreatorCollection|CreatorDto[]
     */
    public function creators(string $topic): static
    {
        return $this->reset("/public/topic/{$topic}/creators/v1")
            ->asCollection(CreatorDto::class, CreatorCollection::class);
    }

    /**
     * Fetch news for a topic. `GET /public/topic/:topic/news/v1`.
     *
     * @return PostCollection|PostDto[]
     */
    public function news(string $topic): static
    {
        return $this->reset("/public/topic/{$topic}/news/v1")->asCollection(PostDto::class, PostCollection::class);
    }

    /**
     * Fetch posts for a topic. `GET /public/topic/:topic/posts/v1`.
     *
     * @return PostCollection|PostDto[]
     */
    public function posts(string $topic): static
    {
        return $this->reset("/public/topic/{$topic}/posts/v1")->asCollection(PostDto::class, PostCollection::class);
    }

    /**
     * Fetch the AI-generated "what's up" summary for a topic. `GET /public/topic/:topic/whatsup/v1`.
     */
    public function whatsUp(string $topic): static
    {
        return $this->reset("/public/topic/{$topic}/whatsup/v1");
    }
}
