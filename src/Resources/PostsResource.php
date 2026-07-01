<?php

declare(strict_types=1);

namespace Tigusigalpa\LunarCrush\Resources;

use Tigusigalpa\LunarCrush\Collections\PostCollection;
use Tigusigalpa\LunarCrush\Collections\TimeSeriesCollection;
use Tigusigalpa\LunarCrush\Dto\PostDto;
use Tigusigalpa\LunarCrush\Dto\TimeSeriesPointDto;

/**
 * Fluent builder for the LunarCrush Posts endpoint group.
 */
final class PostsResource extends AbstractResource
{
    /**
     * List posts. `GET /public/posts/v1`.
     *
     * @return PostCollection|PostDto[]
     */
    public function list(): static
    {
        return $this->reset('/public/posts/v1')->asCollection(PostDto::class, PostCollection::class);
    }

    /**
     * Fetch aggregate post time-series data. `GET /public/posts/time-series/v1`.
     *
     * @return TimeSeriesCollection|TimeSeriesPointDto[]
     */
    public function timeSeries(): static
    {
        return $this->reset('/public/posts/time-series/v1')
            ->asCollection(TimeSeriesPointDto::class, TimeSeriesCollection::class);
    }
}
