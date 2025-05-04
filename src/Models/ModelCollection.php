<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use InvalidArgumentException;
use OutOfBoundsException;

abstract class ModelCollection extends Model implements ModelCollectionInterface
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

    // Iterator interface methods
    public function rewind(): void {
        $this->position = 0;
    }

    public function valid(): bool {
        $keys = array_keys($this->models);
        return isset($keys[$this->position]);
    }

    public function key(): string|int {
        $keys = array_keys($this->models);
        return $keys[$this->position] ?? throw new OutOfBoundsException('Invalid position');
    }

    public function next(): void {
        $this->position++;
    }

    // ArrayAccess interface methods
    public function offsetExists(mixed $offset): bool {
        return isset($this->models[$offset]);
    }

    public function offsetSet(mixed $offset, mixed $value): void {
        if (! $value instanceof ModelInterface) {
            throw new InvalidArgumentException("Must be an Model instance");
        }

        if ($offset === null) {
            $this->models[] = $value;
        } else {
            $this->models[$offset] = $value;
        }
    }

    public function offsetUnset(mixed $offset): void {
        unset($this->models[$offset]);
    }

    // Countable interface
    public function count(): int {
        return count($this->models);
    }

    public function toArray(): array {
        return array_map(fn($model) => $model->toArray(), $this->models);
    }
}
