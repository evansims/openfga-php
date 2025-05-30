<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use ArrayAccess;
use Countable;
use Iterator;
use JsonSerializable;
use OpenFGA\Models\ModelInterface;
use OpenFGA\Schema\CollectionSchemaInterface;
use Override;
use ReturnTypeWillChange;

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
    /**
     * @param T $item
     *
     * @return $this
     */
    public function add($item): static;

    public function clear(): void;

    /**
     * @return int<0, max>
     */
    #[Override]
    public function count(): int;

    /**
     * @return T
     */
    #[Override]
    #[ReturnTypeWillChange]
    public function current(): ModelInterface;

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

    public function isEmpty(): bool;

    /**
     * @return array<int|string, mixed>
     */
    #[Override]
    public function jsonSerialize(): array;

    #[Override]
    public function key(): int;

    #[Override]
    public function next(): void;

    /**
     * @param mixed $offset
     */
    #[Override]
    public function offsetExists(mixed $offset): bool;

    /**
     * @param mixed $offset
     *
     * @return null|T
     */
    #[Override]
    #[ReturnTypeWillChange]
    public function offsetGet(mixed $offset): ?ModelInterface;

    /**
     * @param null|int|string $offset
     * @param T               $value
     */
    #[Override]
    public function offsetSet(mixed $offset, mixed $value): void;

    /**
     * @param mixed $offset
     */
    #[Override]
    public function offsetUnset(mixed $offset): void;

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

    #[Override]
    public function rewind(): void;

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

    #[Override]
    public function valid(): bool;

    /**
     * @param iterable<T>|T ...$items
     *
     * @return static<T>
     */
    public function withItems(...$items): static;

    public static function schema(): CollectionSchemaInterface;
}
