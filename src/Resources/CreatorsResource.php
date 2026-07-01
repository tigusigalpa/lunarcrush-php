<?php

declare(strict_types=1);

namespace Tigusigalpa\LunarCrush\Resources;

use Tigusigalpa\LunarCrush\Collections\CreatorCollection;
use Tigusigalpa\LunarCrush\Collections\PostCollection;
use Tigusigalpa\LunarCrush\Collections\TimeSeriesCollection;
use Tigusigalpa\LunarCrush\Dto\CreatorDto;
use Tigusigalpa\LunarCrush\Dto\PostDto;
use Tigusigalpa\LunarCrush\Dto\TimeSeriesPointDto;
use Tigusigalpa\LunarCrush\Enums\Network;

/**
 * Fluent builder for the LunarCrush Creators endpoint group.
 */
final class CreatorsResource extends AbstractResource
{
    /**
     * Fetch a creator's detail. `GET /public/creator/:network/:id/v1`.
     *
     * @param Network|string $network Social network (`twitter`, `youtube`, `instagram`, `reddit`, `tiktok`).
     * @param string         $id      Creator identifier on the given network.
     */
    public function creator(Network|string $network, string $id): static
    {
        $network = $network instanceof Network ? $network->value : $network;

        return $this->reset("/public/creator/{$network}/{$id}/v1")->asItem(CreatorDto::class);
    }

    /**
     * Fetch posts authored by a creator. `GET /public/creator/:network/:id/posts/v1`.
     *
     * @return PostCollection|PostDto[]
     */
    public function posts(Network|string $network, string $id): static
    {
        $network = $network instanceof Network ? $network->value : $network;

        return $this->reset("/public/creator/{$network}/{$id}/posts/v1")
            ->asCollection(PostDto::class, PostCollection::class);
    }

    /**
     * Fetch time-series history for a creator. `GET /public/creator/:network/:id/time-series/v1`.
     *
     * @return TimeSeriesCollection|TimeSeriesPointDto[]
     */
    public function timeSeries(Network|string $network, string $id): static
    {
        $network = $network instanceof Network ? $network->value : $network;

        return $this->reset("/public/creator/{$network}/{$id}/time-series/v1")
            ->asCollection(TimeSeriesPointDto::class, TimeSeriesCollection::class);
    }

    /**
     * List top creators across the platform. `GET /public/creators/list/v1`.
     *
     * @return CreatorCollection|CreatorDto[]
     */
    public function list(): static
    {
        return $this->reset('/public/creators/list/v1')->asCollection(CreatorDto::class, CreatorCollection::class);
    }
}
