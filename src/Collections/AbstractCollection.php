<?php

declare(strict_types=1);

namespace Tigusigalpa\LunarCrush\Collections;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

/**
 * Base class for typed, read-only collections of DTOs returned by the
 * LunarCrush API list endpoints.
 *
 * @template TItem
 *
 * @implements IteratorAggregate<int, TItem>
 * @implements ArrayAccess<int, TItem>
 */
abstract class AbstractCollection implements IteratorAggregate, Countable, ArrayAccess
{
    /** @var list<TItem> */
    protected array $items;

    /**
     * @param list<TItem> $items
     */
    public function __construct(array $items = [])
    {
        $this->items = array_values($items);
    }

    /**
     * @return list<TItem>
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * @return TItem|null
     */
    public function first(): mixed
    {
        return $this->items[0] ?? null;
    }

    /**
     * @return TItem|null
     */
    public function last(): mixed
    {
        return $this->items === [] ? null : $this->items[array_key_last($this->items)];
    }

    public function isEmpty(): bool
    {
        return $this->items === [];
    }

    public function isNotEmpty(): bool
    {
        return !$this->isEmpty();
    }

    /**
     * @param callable(TItem): bool $callback
     *
     * @return static<TItem>
     */
    public function filter(callable $callback): static
    {
        return new static(array_values(array_filter($this->items, $callback)));
    }

    /**
     * @template TMapped
     *
     * @param callable(TItem): TMapped $callback
     *
     * @return list<TMapped>
     */
    public function map(callable $callback): array
    {
        return array_map($callback, $this->items);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function toArray(): array
    {
        return array_map(
            static fn (mixed $item): array => method_exists($item, 'toArray') ? $item->toArray() : (array) $item,
            $this->items,
        );
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->items[$offset]);
    }

    /**
     * @return TItem
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->items[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if ($offset === null) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->items[$offset]);
    }
}
