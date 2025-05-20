<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use InvalidArgumentException;
use OpenFGA\Exceptions\ModelException;
use OpenFGA\Models\ModelInterface;
use OpenFGA\Schema\{CollectionSchema, CollectionSchemaInterface};
use OutOfBoundsException;
use TypeError;

use function count;
use function gettype;
use function is_a;
use function is_int;
use function is_iterable;
use function is_object;
use function sprintf;

/**
 * @template T
 *
 * @implements IndexedCollectionInterface<T>
 */
abstract class IndexedCollection implements IndexedCollectionInterface
{
    /**
     * @var array<int|string, T>
     */
    private array $models = [];

    private int $position = 0;

    /**
     * @var class-string<T>
     */
    protected static string $itemType;

    /**
     * @param iterable<T>|T ...$items
     *
     * @throws TypeError When item type is not defined or invalid
     */
    public function __construct(iterable | ModelInterface ...$items)
    {
        if (! isset(static::$itemType)) {
            throw new TypeError(sprintf('Undefined item type for %s. Define the $itemType property or override the constructor.', static::class));
        }

        if (! is_a(static::$itemType, ModelInterface::class, true)) {
            throw new TypeError(sprintf('Expected item type to implement %s, %s given', ModelInterface::class, static::$itemType));
        }

        foreach ($items as $item) {
            if (is_iterable($item)) {
                $this->addItems($item);
            } else {
                $this->add($item);
            }
        }
    }

    /**
     * @param T $item
     *
     * @return $this
     */
    public function add(ModelInterface $item): static
    {
        if (! $item instanceof static::$itemType) {
            throw new TypeError(sprintf('Expected instance of %s, %s given', static::$itemType, $item::class));
        }

        $this->models[] = $item;

        return $this;
    }

    public function clear(): void
    {
        $this->models = [];
        $this->position = 0;
    }

    public function count(): int
    {
        return count($this->models);
    }

    /**
     * @return T
     */
    public function current(): ModelInterface
    {
        $key = $this->key();

        if (null === $key) {
            throw new OutOfBoundsException('Invalid position');
        }

        return $this->models[$key];
    }

    /**
     * Checks if all items match the callback.
     *
     * @param callable(T): bool $callback
     */
    public function every(callable $callback): bool
    {
        foreach ($this->models as $item) {
            if (! $callback($item)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Filters the collection using a callback.
     *
     * @param callable(T): bool $callback
     *
     * @return static<T>
     */
    public function filter(callable $callback): static
    {
        /** @var class-string<static> $collection */
        $collection = static::class;

        /** @var IndexedCollection<ModelInterface> $new */
        $new = new $collection();
        foreach ($this->models as $item) {
            if ($callback($item)) {
                $new->add($item);
            }
        }

        return $new;
    }

    /**
     * Returns the first item that matches the callback.
     *
     * @param callable(T): bool $callback
     *
     * @return null|T
     */
    public function first(?callable $callback = null): ?ModelInterface
    {
        if (null === $callback) {
            return $this->models[0] ?? null;
        }

        foreach ($this->models as $item) {
            if ($callback($item)) {
                return $item;
            }
        }

        return null;
    }

    /**
     * @param int $offset
     *
     * @return null|T
     */
    public function get(int $offset): ?ModelInterface
    {
        return $this->models[$offset] ?? null;
    }

    public function isEmpty(): bool
    {
        return empty($this->models);
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

    /**
     * Maps the collection to another collection.
     *
     * @template U of ModelInterface
     *
     * @param class-string<U> $targetType
     * @param callable(T): U  $callback
     *
     * @return static<U>
     */
    public function map(string $targetType, callable $callback): static
    {
        if (! is_a($targetType, ModelInterface::class, true)) {
            throw ModelException::invalidItemType($targetType);
        }

        $new = new static();

        $new::$itemType = $targetType;
        foreach ($this->models as $item) {
            $mapped = $callback($item);
            if (! $mapped instanceof $targetType) {
                throw ModelException::typeMismatch($targetType, $mapped::class);
            }
            $new->add($mapped);
        }

        return $new;
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->models[$offset]);
    }

    /**
     * @param mixed $offset
     *
     * @return null|T
     */
    public function offsetGet(mixed $offset): ?ModelInterface
    {
        return $this->models[$offset] ?? null;
    }

    /**
     * @param null|int|string $offset
     * @param T               $value
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (! $value instanceof static::$itemType) {
            throw new InvalidArgumentException(sprintf('Expected instance of %s, %s given.', static::$itemType, is_object($value) ? $value::class : gettype($value)));
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

    /**
     * Reduces the collection to a single value.
     *
     * @template U
     *
     * @param U                 $initial
     * @param callable(U, T): U $callback
     *
     * @return U
     */
    public function reduce(mixed $initial, callable $callback): mixed
    {
        $result = $initial;
        foreach ($this->models as $item) {
            $result = $callback($result, $item);
        }

        return $result;
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    /**
     * Checks if any item matches the callback.
     *
     * @param callable(T): bool $callback
     */
    public function some(callable $callback): bool
    {
        foreach ($this->models as $item) {
            if ($callback($item)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<int, T>
     */
    public function toArray(): array
    {
        return $this->models;
    }

    public function valid(): bool
    {
        $keys = array_keys($this->models);

        return isset($keys[$this->position]);
    }

    /**
     * @param iterable<T>|T ...$items
     *
     * @return static<T>
     */
    public function withItems(iterable | ModelInterface ...$items): static
    {
        $new = clone $this;
        $new->addItems(...$items);

        return $new;
    }

    public static function schema(): CollectionSchemaInterface
    {
        if (! isset(static::$itemType)) {
            throw ModelException::undefinedItemType(static::class);
        }

        if (! is_a(static::$itemType, ModelInterface::class, true)) {
            throw ModelException::invalidItemType(static::$itemType);
        }

        return new CollectionSchema(
            className: static::class,
            itemType: static::$itemType,
            requireItems: false,
        );
    }

    /**
     * @param iterable<T>|T ...$items
     */
    protected function addItems(iterable | ModelInterface ...$items): void
    {
        foreach ($items as $item) {
            if (is_iterable($item)) {
                $this->addItems(...$item);
            } else {
                $this->add($item);
            }
        }
    }
}
