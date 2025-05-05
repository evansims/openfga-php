<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use ArrayAccess;
use Countable;
use Iterator;

interface ModelCollectionInterface extends ArrayAccess, Countable, Iterator, ModelInterface
{
    public function count(): int;

    public function key(): string | int;

    public function next(): void;

    public function offsetExists(mixed $offset): bool;

    public function offsetSet(mixed $offset, mixed $value): void;

    public function offsetUnset(mixed $offset): void;

    public function rewind(): void;

    public function valid(): bool;
}
