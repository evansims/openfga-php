<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use ArrayAccess;
use Countable;
use Iterator;
use JsonSerializable;
use OpenFGA\Exceptions\{ClientThrowable};
use OpenFGA\Models\ModelInterface;
use OpenFGA\Schema\CollectionSchemaInterface;
use Override;
use ReturnTypeWillChange;

/**
 * Represents a collection that is indexed by a string, like a JSON object.
 *
 * @template T of ModelInterface
 *
 * @extends ArrayAccess<string, T>
 * @extends Iterator<string, T>
 */
interface KeyedCollectionInterface extends ArrayAccess, Countable, Iterator, JsonSerializable
{
    /**
     * Get the schema definition for this collection type.
     *
     * Returns the schema that defines the structure and validation rules
     * for this collection, including the expected item type and constraints.
     *
     * @throws ClientThrowable If the item type is not properly defined
     *
     * @return CollectionSchemaInterface The collection schema
     */
    public static function schema(): CollectionSchemaInterface;

    /**
     * Add an item to the collection with the specified key.
     *
     * This method associates an item with a string key, allowing for
     * named access to collection elements similar to an associative array.
     *
     * @param string $key  The string key to associate with the item
     * @param T      $item The item to add to the collection
     *
     * @throws ClientThrowable If the item is not of the expected type
     *
     * @return $this The collection instance for method chaining
     */
    public function add(string $key, ModelInterface $item): static;

    /**
     * @return int<0, max>
     */
    #[Override]
    public function count(): int;

    /**
     * @throws ClientThrowable If the current key is invalid
     *
     * @return T
     */
    #[Override]
    #[ReturnTypeWillChange]
    public function current(): ModelInterface;

    /**
     * Get an item by its string key.
     *
     * This method retrieves the item associated with the specified key.
     * Returns null if no item is found with the given key.
     *
     * @param  string $key The key of the item to retrieve
     * @return T|null The item associated with the key, or null if not found
     */
    public function get(string $key);

    /**
     * Check if a key exists in the collection.
     *
     * This method determines whether the collection contains an item
     * associated with the specified key.
     *
     * @param  string $key The key to check for existence
     * @return bool   True if the key exists, false otherwise
     */
    public function has(string $key): bool;

    /**
     * Check if the collection contains no items.
     *
     * This method provides a convenient way to test whether the collection
     * is empty without needing to check the count.
     *
     * @return bool True if the collection is empty, false otherwise
     */
    public function isEmpty(): bool;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function jsonSerialize(): array;

    /**
     * @throws ClientThrowable If the key is not a string
     */
    #[Override]
    public function key(): string;

    #[Override]
    public function next(): void;

    /**
     * @param mixed $offset
     */
    #[Override]
    public function offsetExists(mixed $offset): bool;

    /**
     * Get an item by its offset key.
     *
     * @param  mixed  $offset
     * @return T|null
     */
    #[Override]
    #[ReturnTypeWillChange]
    public function offsetGet(mixed $offset): ?ModelInterface;

    /**
     * @param string|null $offset
     * @param T           $value
     *
     * @throws ClientThrowable If the value is not of the expected type
     */
    #[Override]
    public function offsetSet(mixed $offset, mixed $value): void;

    /**
     * @param mixed $offset
     */
    #[Override]
    public function offsetUnset(mixed $offset): void;

    #[Override]
    public function rewind(): void;

    /**
     * Convert the collection to a standard PHP associative array.
     *
     * This method creates a native PHP associative array containing all items
     * in the collection, preserving their string keys and values.
     *
     * @return array<string, T> An associative array containing all collection items
     */
    public function toArray(): array;

    #[Override]
    public function valid(): bool;
}
