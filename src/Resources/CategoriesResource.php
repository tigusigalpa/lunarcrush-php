<?php

declare(strict_types=1);

namespace Tigusigalpa\LunarCrush\Resources;

use Tigusigalpa\LunarCrush\Collections\CategoryCollection;
use Tigusigalpa\LunarCrush\Collections\CreatorCollection;
use Tigusigalpa\LunarCrush\Collections\PostCollection;
use Tigusigalpa\LunarCrush\Collections\TimeSeriesCollection;
use Tigusigalpa\LunarCrush\Collections\TopicCollection;
use Tigusigalpa\LunarCrush\Dto\CategoryDto;
use Tigusigalpa\LunarCrush\Dto\CreatorDto;
use Tigusigalpa\LunarCrush\Dto\PostDto;
use Tigusigalpa\LunarCrush\Dto\TimeSeriesPointDto;
use Tigusigalpa\LunarCrush\Dto\TopicDto;

/**
 * Fluent builder for the LunarCrush Categories endpoint group.
 */
final class CategoriesResource extends AbstractResource
{
    /**
     * List all categories. `GET /public/categories/list/v1`.
     *
     * @return CategoryCollection|CategoryDto[]
     */
    public function list(): static
    {
        return $this->reset('/public/categories/list/v1')
            ->asCollection(CategoryDto::class, CategoryCollection::class);
    }

    /**
     * Fetch a category summary. `GET /public/category/:category/v1`.
     */
    public function category(string $category): static
    {
        return $this->reset('/public/category/' . $this->encodePathSegment($category) . '/v1')->asItem(CategoryDto::class);
    }

    /**
     * Fetch top creators for a category. `GET /public/category/:category/creators/v1`.
     *
     * @return CreatorCollection|CreatorDto[]
     */
    public function creators(string $category): static
    {
        return $this->reset('/public/category/' . $this->encodePathSegment($category) . '/creators/v1')
            ->asCollection(CreatorDto::class, CreatorCollection::class);
    }

    /**
     * Fetch news for a category. `GET /public/category/:category/news/v1`.
     *
     * @return PostCollection|PostDto[]
     */
    public function news(string $category): static
    {
        return $this->reset('/public/category/' . $this->encodePathSegment($category) . '/news/v1')
            ->asCollection(PostDto::class, PostCollection::class);
    }

    /**
     * Fetch posts for a category. `GET /public/category/:category/posts/v1`.
     *
     * @return PostCollection|PostDto[]
     */
    public function posts(string $category): static
    {
        return $this->reset('/public/category/' . $this->encodePathSegment($category) . '/posts/v1')
            ->asCollection(PostDto::class, PostCollection::class);
    }

    /**
     * Fetch time-series history for a category. `GET /public/category/:category/time-series/v1`.
     *
     * @return TimeSeriesCollection|TimeSeriesPointDto[]
     */
    public function timeSeries(string $category): static
    {
        return $this->reset('/public/category/' . $this->encodePathSegment($category) . '/time-series/v1')
            ->asCollection(TimeSeriesPointDto::class, TimeSeriesCollection::class);
    }

    /**
     * Fetch the topics that make up a category. `GET /public/category/:category/topics/v1`.
     *
     * @return TopicCollection|TopicDto[]
     */
    public function topics(string $category): static
    {
        return $this->reset('/public/category/' . $this->encodePathSegment($category) . '/topics/v1')
            ->asCollection(TopicDto::class, TopicCollection::class);
    }
}
