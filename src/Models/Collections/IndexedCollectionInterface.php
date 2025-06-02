<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use ArrayAccess;
use Countable;
use Iterator;
use JsonSerializable;
use OpenFGA\Exceptions\{ClientException, ClientThrowable};
use OpenFGA\Models\ModelInterface;
use OpenFGA\Schema\CollectionSchemaInterface;
use Override;
use ReturnTypeWillChange;

/**
 * Represents a type-safe collection indexed by integers, similar to a JSON array.
 *
 * This interface provides a type-safe, feature-rich collection implementation for
 * OpenFGA model objects that maintains array-like semantics while offering
 * additional functionality for filtering, transformation, and validation.
 *
 * IndexedCollections are used throughout the OpenFGA SDK to manage:
 * - Lists of relationship tuples
 * - Collections of type definitions in authorization models
 * - Arrays of conditions and parameters
 * - Sets of user objects and userset operations
 * - Sequences of authorization model versions
 *
 * The collection provides both familiar array-style access (through ArrayAccess)
 * and enhanced functional programming methods (filter, map, reduce) that make
 * working with authorization data more expressive and safer. All items in the
 * collection are validated to ensure they implement the expected ModelInterface.
 *
 * Collections support both mutable operations (like `add()` and `clear()`) for
 * incremental building and immutable operations (like `withItems()` and `filter()`)
 * for functional programming patterns, providing flexibility while maintaining
 * data integrity in authorization contexts where consistency is critical.
 *
 * @template T of ModelInterface
 *
 * @extends ArrayAccess<int, T>
 * @extends Iterator<int, T>
 *
 * @see ModelInterface Base interface for all OpenFGA models
 * @see CollectionSchemaInterface Collection validation system
 */
interface IndexedCollectionInterface extends ArrayAccess, Countable, Iterator, JsonSerializable
{
    /**
     * Get the schema definition for this collection type.
     *
     * Returns the schema that defines the structure, validation rules, and
     * serialization behavior for this collection type. The schema specifies
     * the expected item type, validation constraints, and transformation
     * rules that ensure all items in the collection conform to OpenFGA
     * data requirements.
     *
     * Collection schemas enable:
     * - Type validation for all added items
     * - Consistent serialization across different contexts
     * - API compatibility verification
     * - Runtime type checking and error reporting
     *
     * The schema system ensures that authorization data maintains integrity
     * throughout processing, preventing type mismatches that could lead to
     * authorization failures or security vulnerabilities.
     *
     * @throws ClientThrowable If the item type is not properly defined or schema configuration is invalid
     *
     * @return CollectionSchemaInterface The schema definition containing validation rules and type constraints for this collection
     */
    public static function schema(): CollectionSchemaInterface;

    /**
     * Add an item to the end of the collection.
     *
     * This method appends a new model object to the collection, automatically
     * assigning it the next available integer index. The item is validated to
     * ensure it matches the expected type for this collection, maintaining
     * type safety throughout the authorization data processing pipeline.
     *
     * This operation modifies the current collection instance directly, making
     * it suitable for building collections incrementally. For immutable operations,
     * use the `withItems()` method instead, which creates new collection instances
     * without modifying the original.
     *
     * @param T $item The OpenFGA model object to add to the collection
     *
     * @throws ClientThrowable If the item is not of the expected type or fails validation
     *
     * @return $this The same collection instance for method chaining
     */
    public function add($item): static;

    /**
     * Remove all items from the collection.
     *
     * This method empties the collection, resetting it to its initial state
     * with no items and a count of zero.
     */
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
     * Check if all items in the collection match the given condition.
     *
     * This method tests whether all items in the collection satisfy the
     * provided callback function. Returns true if all items pass the test,
     * false if any item fails.
     *
     * @param  callable(T): bool $callback A function that tests each item and returns a boolean
     * @return bool              True if all items match the condition, false otherwise
     */
    public function every(callable $callback): bool;

    /**
     * Create a new collection containing only items that match the condition.
     *
     * This method creates a new collection containing only the items from the
     * current collection that satisfy the provided callback function.
     *
     * @param  callable(T): bool $callback A function that tests each item and returns a boolean
     * @return static            A new collection containing only the matching items
     */
    public function filter(callable $callback): static;

    /**
     * Get the first item in the collection, optionally matching a condition.
     *
     * When called without a callback, returns the first item in the collection.
     * When called with a callback, returns the first item that satisfies the condition.
     *
     * @param  callable(T): bool|null $callback Optional function to test each item
     * @return T|null                 The first matching item, or null if none found
     */
    public function first(?callable $callback = null);

    /**
     * Get an item by its position in the collection.
     *
     * This method retrieves the item at the specified index position.
     * Returns null if the index is out of bounds.
     *
     * @param  int    $offset The index position of the item to retrieve
     * @return T|null The item at the specified position, or null if not found
     */
    public function get(int $offset);

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
     * @return array<int|string, mixed>
     */
    #[Override]
    public function jsonSerialize(): array;

    /**
     * Get the current iterator key.
     *
     * This method returns the current position in the collection iteration,
     * which is always an integer for indexed collections.
     *
     * @return int The current iterator position
     */
    #[Override]
    public function key(): int;

    /**
     * Move the iterator to the next position.
     *
     * This method advances the internal iterator pointer to the next
     * item in the collection sequence.
     */
    #[Override]
    public function next(): void;

    /**
     * Check if an offset exists in the collection.
     *
     * This method determines whether the collection contains an item
     * at the specified offset position.
     *
     * @param  mixed $offset The offset to check for existence
     * @return bool  True if the offset exists, false otherwise
     */
    #[Override]
    public function offsetExists(mixed $offset): bool;

    /**
     * @param  mixed  $offset
     * @return T|null
     */
    #[Override]
    #[ReturnTypeWillChange]
    public function offsetGet(mixed $offset): ?ModelInterface;

    /**
     * @param int|string|null $offset
     * @param T               $value
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

    /**
     * Reduce the collection to a single value using a callback function.
     *
     * This method iteratively applies a callback function to accumulate the
     * collection items into a single value, starting with an initial value.
     *
     * @template U
     *
     * @param  U                 $initial  The initial value to start the reduction
     * @param  callable(U, T): U $callback A function that accepts the accumulator and current item
     * @return U                 The final accumulated value
     */
    public function reduce(mixed $initial, callable $callback): mixed;

    /**
     * Reset the iterator to the beginning of the collection.
     *
     * This method moves the internal iterator pointer back to the
     * first item in the collection.
     */
    #[Override]
    public function rewind(): void;

    /**
     * Check if at least one item in the collection matches the given condition.
     *
     * This method tests whether at least one item in the collection satisfies the
     * provided callback function. Returns true if any item passes the test,
     * false if all items fail.
     *
     * @param  callable(T): bool $callback A function that tests each item and returns a boolean
     * @return bool              True if any item matches the condition, false otherwise
     */
    public function some(callable $callback): bool;

    /**
     * Convert the collection to a standard PHP array.
     *
     * This method creates a native PHP array containing all items
     * in the collection, preserving their order and indexes.
     *
     * @return array<int|string, T> A standard PHP array containing all collection items
     */
    public function toArray(): array;

    /**
     * Check if the current iterator position is valid.
     *
     * This method determines whether the current iterator position
     * points to a valid item in the collection.
     *
     * @return bool True if the current position is valid, false otherwise
     */
    #[Override]
    public function valid(): bool;

    /**
     * Create a new collection with the specified items.
     *
     * This method creates a fresh collection instance containing only
     * the provided items, leaving the original collection unchanged.
     *
     * @param iterable<T>|T ...$items Items to include in the new collection
     *
     * @throws ClientException If any item is not of the expected type
     *
     * @return static A new collection instance containing the specified items
     */
    public function withItems(...$items): static;
}
