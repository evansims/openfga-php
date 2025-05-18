<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use InvalidArgumentException;
use OutOfBoundsException;

use function count;
use function is_int;

trait CollectionTrait
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

    public function jsonSerialize(): array
    {
        return array_map(static fn ($model) => $model->jsonSerialize(), $this->models);
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
        if (isset($this->models[$offset])) {
            $isNumeric = is_int($offset);
            unset($this->models[$offset]);
            // Reindex the array to maintain sequential numeric keys if the offset was numeric
            if ($isNumeric) {
                $this->models = array_values($this->models);
                // Reset the position after reindexing
                $this->position = 0;
            }
        }
    }

    // Iterator interface methods
    final public function rewind(): void
    {
        $this->position = 0;
    }

    final public function valid(): bool
    {
        $keys = array_keys($this->models);

        return isset($keys[$this->position]);
    }
}
