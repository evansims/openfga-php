<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use InvalidArgumentException;
use OutOfBoundsException;

use function count;

abstract class ModelCollection implements ModelCollectionInterface
{
    /**
     * Array of ModelInterface objects.
     *
     * @var array<int|string, ModelInterface>
     */
    protected array $models = [];

    /**
     * Current position in the collection.
     *
     * @var int
     */
    protected int $position = 0;

    // Countable interface
    final public function count(): int
    {
        return count($this->models);
    }

    final public function key(): string | int
    {
        $keys = array_keys($this->models);

        return $keys[$this->position] ?? throw new OutOfBoundsException('Invalid position');
    }

    final public function next(): void
    {
        ++$this->position;
    }

    // ArrayAccess interface methods
    final public function offsetExists(mixed $offset): bool
    {
        return isset($this->models[$offset]);
    }

    final public function offsetSet(mixed $offset, mixed $value): void
    {
        if (! $value instanceof ModelInterface) {
            throw new InvalidArgumentException('Must be an Model instance');
        }

        if (null === $offset) {
            $this->models[] = $value;
        } else {
            $this->models[$offset] = $value;
        }
    }

    final public function offsetUnset(mixed $offset): void
    {
        unset($this->models[$offset]);
    }

    // Iterator interface methods
    final public function rewind(): void
    {
        $this->position = 0;
    }

    final public function toArray(): array
    {
        return array_map(static fn ($model) => $model->toArray(), $this->models);
    }

    final public function valid(): bool
    {
        $keys = array_keys($this->models);

        return isset($keys[$this->position]);
    }
}
