<?php

namespace OpenFGA\Models;

use Iterator;
use ArrayAccess;
use Countable;

interface ModelCollectionInterface extends ModelInterface, Iterator, ArrayAccess, Countable
{
    public function rewind(): void;

    public function valid(): bool;

    public function key(): string|int;

    public function next(): void;

    public function offsetExists(mixed $offset): bool;

    public function offsetSet(mixed $offset, mixed $value): void;

    public function offsetUnset(mixed $offset): void;

    public function count(): int;
}
