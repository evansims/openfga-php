<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use ArrayAccess;
use Countable;
use Iterator;
use JsonSerializable;
use OpenFGA\Schema\CollectionSchemaInterface;
use OpenFGA\Models\ModelInterface;

/**
 * Represents a collection that is indexed by an integer, like a JSON array.
 *
 * @template T of ModelInterface
 *
 * @extends ArrayAccess<int, T>
 * @extends Iterator<int, T>
 */
interface IndexedCollectionInterface extends ArrayAccess, Countable, Iterator, JsonSerializable
{
    public function count(): int;

    public function key(): int;

    public function next(): void;

    public function offsetExists(mixed $offset): bool;

    /**
     * @param T $item
     * @return $this
     */
    public function add($item): static;

    /**
     * @return T
     */
    #[\ReturnTypeWillChange]
    public function current();

    /**
     * Checks if all items match the callback.
     *
     * @param callable(T): bool $callback
     */
    public function every(callable $callback): bool;

    /**
     * Filters the collection using a callback.
     *
     * @param callable(T): bool $callback
     *
     * @return static<T>
     */
    public function filter(callable $callback): static;

    /**
     * Returns the first item that matches the callback.
     *
     * @param callable(T): bool $callback
     *
     * @return null|T
     */
    public function first(?callable $callback = null);

    /**
     * @param int $offset
     *
     * @return null|T
     */
    public function get(int $offset);

    /**
     * @param null|int|string $offset
     * @param T               $value
     */
    public function offsetSet(mixed $offset, mixed $value): void;

    /**
     * @param mixed $offset
     *
     * @return null|T
     */
    #[\ReturnTypeWillChange]
    public function offsetGet(mixed $offset);

    public function offsetUnset(mixed $offset): void;

    public function rewind(): void;

    public function valid(): bool;

    public static function schema(): CollectionSchemaInterface;

    /**
     * Maps the collection to another collection.
     *
     * @template U of ModelInterface
     *
     * @param class-string<U> $targetType
     * @param callable(T): U  $callback
     *
     * @return static<U>
     */
    public function map(string $targetType, callable $callback): static;

    /**
     * Reduces the collection to a single value.
     *
     * @template U
     *
     * @param U                 $initial
     * @param callable(U, T): U $callback
     *
     * @return U
     */
    public function reduce(mixed $initial, callable $callback): mixed;

    /**
     * Checks if any item matches the callback.
     *
     * @param callable(T): bool $callback
     */
    public function some(callable $callback): bool;

    /**
     * @return array<int|string, T>
     */
    public function toArray(): array;

    /**
     * @param iterable<T>|T ...$items
     *
     * @return static<T>
     */
    public function withItems(...$items): static;

    /**
     * @return array<int|string, mixed>
     */
    public function jsonSerialize(): array;
}
