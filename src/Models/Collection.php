<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use InvalidArgumentException;
use OpenFGA\Exceptions\ModelException;
use OpenFGA\Schema\{CollectionSchema, CollectionSchemaInterface};
use OutOfBoundsException;

use function array_keys;
use function array_map;
use function array_values;
use function count;
use function is_a;
use function is_int;

/**
 * Base collection implementation without traits or abstract classes.
 *
 * @template T of ModelInterface
 *
 * @implements CollectionInterface<T>
 */
class Collection implements CollectionInterface
{
    /**
     * @var array<int|string, T>
     */
    protected array $models = [];

    protected int $position = 0;

    /**
     * @var class-string<T>
     */
    protected static string $itemType;

    protected static ?CollectionSchemaInterface $schema = null;

    public function count(): int
    {
        return count($this->models);
    }

    public function current(): mixed
    {
        $key = $this->key();
        return $this->models[$key] ?? null;
    }

    public function jsonSerialize(): array
    {
        return array_map(static fn (ModelInterface $model) => $model->jsonSerialize(), $this->models);
    }

    public function key(): string | int
    {
        $keys = array_keys($this->models);

        return $keys[$this->position] ?? throw new OutOfBoundsException('Invalid position');
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->models[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->models[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
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

    public function offsetUnset(mixed $offset): void
    {
        if (isset($this->models[$offset])) {
            $isNumeric = is_int($offset);
            unset($this->models[$offset]);
            if ($isNumeric) {
                $this->models = array_values($this->models);
                $this->position = 0;
            }
        }
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function valid(): bool
    {
        $keys = array_keys($this->models);

        return isset($keys[$this->position]);
    }

    public static function schema(): CollectionSchemaInterface
    {
        if (null === self::$schema) {
            if (! isset(self::$itemType)) {
                throw ModelException::undefinedItemType(self::class);
            }

            if (! is_a(self::$itemType, ModelInterface::class, true)) {
                throw ModelException::invalidItemType(self::$itemType);
            }

            self::$schema = new CollectionSchema(
                className: self::class,
                itemType: self::$itemType,
                requireItems: false,
            );
        }

        return self::$schema;
    }
}
