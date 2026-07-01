<?php

declare(strict_types=1);

namespace Tigusigalpa\LunarCrush\Resources;

use Tigusigalpa\LunarCrush\Collections\SearchCollection;
use Tigusigalpa\LunarCrush\Dto\SearchDto;

/**
 * Fluent builder for the LunarCrush Searches (custom aggregations) endpoint group.
 */
final class SearchesResource extends AbstractResource
{
    /**
     * Create a custom search aggregation. `GET /public/searches/create`.
     *
     * @param array<string, mixed> $params Search configuration parameters (e.g. `name`, `terms`).
     */
    public function create(array $params = []): static
    {
        return $this->reset('/public/searches/create')->withParams($params)->asItem(SearchDto::class);
    }

    /**
     * List existing saved searches. `GET /public/searches/list`.
     *
     * @return SearchCollection|SearchDto[]
     */
    public function list(): static
    {
        return $this->reset('/public/searches/list')->asCollection(SearchDto::class, SearchCollection::class);
    }

    /**
     * Search within existing aggregations. `GET /public/searches/search`.
     *
     * @return SearchCollection|SearchDto[]
     */
    public function search(string $term): static
    {
        return $this->reset('/public/searches/search')
            ->withParam('term', $term)
            ->asCollection(SearchDto::class, SearchCollection::class);
    }

    /**
     * Fetch a saved search aggregation summary. `GET /public/searches/:slug`.
     */
    public function show(string $slug): static
    {
        return $this->reset("/public/searches/{$slug}")->asItem(SearchDto::class);
    }

    /**
     * Update an existing search aggregation. `GET /public/searches/:slug/update`.
     *
     * @param array<string, mixed> $params Updated search configuration parameters.
     */
    public function update(string $slug, array $params = []): static
    {
        return $this->reset("/public/searches/{$slug}/update")->withParams($params)->asItem(SearchDto::class);
    }

    /**
     * Delete a search aggregation. `GET /public/searches/:slug/delete`.
     */
    public function delete(string $slug): static
    {
        return $this->reset("/public/searches/{$slug}/delete");
    }
}
