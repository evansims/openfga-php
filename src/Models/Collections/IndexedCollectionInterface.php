<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use ArrayAccess;
use Countable;
use Iterator;
use JsonSerializable;
use OpenFGA\Schema\CollectionSchemaInterface;

/**
 * Represents a collection that is indexed by an integer, like a JSON array.
 *
 * @template T
 *
 * @extends ArrayAccess<int, T>
 * @extends Iterator<int, T>
 */
interface IndexedCollectionInterface extends ArrayAccess, Countable, Iterator, JsonSerializable
{
    public function count(): int;

    public function key(): string | int;

    public function next(): void;

    public function offsetExists(mixed $offset): bool;

    public function offsetSet(mixed $offset, mixed $value): void;

    public function offsetUnset(mixed $offset): void;

    public function rewind(): void;

    public function valid(): bool;

    public static function Schema(): CollectionSchemaInterface;
}
