<?php

declare(strict_types=1);

namespace Tigusigalpa\LunarCrush\Resources;

use Tigusigalpa\LunarCrush\Collections\AbstractCollection;
use Tigusigalpa\LunarCrush\LunarCrushClient;

/**
 * Base class for all LunarCrush resource groups.
 *
 * Provides the fluent query-building interface (sorting, pagination,
 * time-series windowing, and arbitrary parameters) shared by every
 * resource, along with the terminal {@see self::get()} method that
 * dispatches the HTTP request and hydrates the response into DTOs.
 */
abstract class AbstractResource
{
    /** @var array<string, mixed> */
    protected array $query = [];

    protected string $path = '';

    /** @var class-string|null Fully-qualified DTO class used to hydrate each item, or null for a raw array. */
    protected ?string $dtoClass = null;

    /** @var class-string<AbstractCollection>|null Collection class wrapping hydrated DTOs for list responses. */
    protected ?string $collectionClass = null;

    protected bool $isCollection = false;

    public function __construct(protected readonly LunarCrushClient $client)
    {
    }

    /**
     * Sort results by the given field.
     */
    public function sortBy(string $field): static
    {
        $this->query['sort'] = $field;

        return $this;
    }

    /**
     * Limit the number of results returned.
     */
    public function limit(int $limit): static
    {
        $this->query['limit'] = $limit;

        return $this;
    }

    /**
     * Skip to the given page of results (for endpoints that support pagination).
     */
    public function page(int $page): static
    {
        $this->query['page'] = $page;

        return $this;
    }

    /**
     * Sort results in descending order.
     */
    public function desc(): static
    {
        $this->query['desc'] = 'true';

        return $this;
    }

    /**
     * Sort results in ascending order.
     */
    public function asc(): static
    {
        unset($this->query['desc']);

        return $this;
    }

    /**
     * Set the time bucket used for time-series aggregation (e.g. `hour`, `day`).
     */
    public function bucket(string $bucket): static
    {
        $this->query['bucket'] = $bucket;

        return $this;
    }

    /**
     * Set the relative time-series interval (e.g. `1w`, `1m`, `3m`, `1y`, `all`).
     */
    public function interval(string $interval): static
    {
        $this->query['interval'] = $interval;

        return $this;
    }

    /**
     * Set the start of a custom time-series range as a Unix timestamp.
     */
    public function start(int $timestamp): static
    {
        $this->query['start'] = $timestamp;

        return $this;
    }

    /**
     * Set the end of a custom time-series range as a Unix timestamp.
     */
    public function end(int $timestamp): static
    {
        $this->query['end'] = $timestamp;

        return $this;
    }

    /**
     * Set an arbitrary query string parameter not covered by a dedicated method.
     */
    public function withParam(string $key, mixed $value): static
    {
        $this->query[$key] = $value;

        return $this;
    }

    /**
     * Merge an arbitrary set of query string parameters.
     *
     * @param array<string, mixed> $params
     */
    public function withParams(array $params): static
    {
        $this->query = array_merge($this->query, $params);

        return $this;
    }

    /**
     * Execute the request and return the raw decoded JSON response, bypassing DTO hydration.
     *
     * @return array<string, mixed>
     */
    public function raw(): array
    {
        return $this->client->request('GET', $this->path, $this->query);
    }

    /**
     * Execute the built request against the LunarCrush API and hydrate the response.
     *
     * @return AbstractCollection<object>|object|array<string, mixed>
     */
    public function get(): mixed
    {
        return $this->hydrate($this->raw());
    }

    /**
     * Map a raw API response into DTOs/collections based on the resource's current state.
     *
     * @param array<string, mixed> $response
     *
     * @return AbstractCollection<object>|object|array<string, mixed>
     */
    protected function hydrate(array $response): mixed
    {
        $data = $response['data'] ?? $response;

        if ($this->dtoClass === null) {
            return $data;
        }

        if ($this->isCollection) {
            $dtoClass = $this->dtoClass;
            $items = array_map(
                static fn (array $item) => $dtoClass::fromArray($item),
                is_array($data) ? $data : [],
            );

            return $this->collectionClass !== null ? new ($this->collectionClass)($items) : $items;
        }

        return $this->dtoClass::fromArray(is_array($data) ? $data : []);
    }

    /**
     * Reset the builder's path/query/hydration state and start building a new request.
     */
    protected function reset(string $path): static
    {
        $this->path = $path;
        $this->query = [];
        $this->dtoClass = null;
        $this->collectionClass = null;
        $this->isCollection = false;

        return $this;
    }

    /**
     * Configure hydration of a single DTO for the current path.
     *
     * @param class-string $dtoClass
     */
    protected function asItem(string $dtoClass): static
    {
        $this->dtoClass = $dtoClass;
        $this->isCollection = false;

        return $this;
    }

    /**
     * Configure hydration of a DTO collection for the current path.
     *
     * @param class-string                     $dtoClass
     * @param class-string<AbstractCollection>|null $collectionClass
     */
    protected function asCollection(string $dtoClass, ?string $collectionClass = null): static
    {
        $this->dtoClass = $dtoClass;
        $this->collectionClass = $collectionClass;
        $this->isCollection = true;

        return $this;
    }
}
