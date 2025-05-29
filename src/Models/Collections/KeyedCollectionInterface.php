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
     * @param T      $item
     * @param string $key
     *
     * @return $this
     */
    public function add(string $key, ModelInterface $item): static;

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
     * @param string $key
     *
     * @return null|T
     */
    public function get(string $key);

    /**
     * Check if a key exists in the collection.
     *
     * @param string $key
     */
    public function has(string $key): bool;

    public function isEmpty(): bool;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function jsonSerialize(): array;

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
     * @param null|string $offset
     * @param T           $value
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
     * @return array<string, T>
     */
    public function toArray(): array;

    #[Override]
    public function valid(): bool;

    public static function schema(): CollectionSchemaInterface;
}
