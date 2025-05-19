<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use ArrayAccess;
use Countable;
use InvalidArgumentException;
use Iterator;
use JsonSerializable;
use OpenFGA\Exceptions\ModelException;
use OpenFGA\Schema\CollectionSchema;
use OpenFGA\Schema\CollectionSchemaInterface;
use OutOfBoundsException;
use TypeError;

use function array_keys;
use function array_map;
use function array_values;
use function count;
use function is_a;

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

    public function jsonSerialize(): array
    {
        return array_map(static fn (ModelInterface $model) => $model->jsonSerialize(), $this->models);
    }

    public function key(): string|int
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
                $this->models   = array_values($this->models);
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
        if (null === static::$schema) {
            if (! isset(static::$itemType)) {
                throw ModelException::undefinedItemType(static::class);
            }

            if (! is_a(static::$itemType, ModelInterface::class, true)) {
                throw ModelException::invalidItemType(static::$itemType);
            }

            static::$schema = new CollectionSchema(
                className: static::class,
                itemType: static::$itemType,
                requireItems: false,
            );
        }

        return static::$schema;
    }
}
