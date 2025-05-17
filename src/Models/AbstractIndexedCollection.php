<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use ArrayAccess;
use Countable;
use Iterator;
use JsonSerializable;
use OpenFGA\Exceptions\ModelException;
use OpenFGA\Schema\{CollectionSchema, CollectionSchemaInterface};

use function is_iterable;

/**
 * @template T of ModelInterface
 */
abstract class AbstractIndexedCollection implements ArrayAccess, Countable, Iterator, JsonSerializable
{
    use CollectionTrait;

    /**
     * @var class-string<T>
     */
    protected static string $itemType;

    protected static ?CollectionSchemaInterface $schema = null;

    /**
     * @param iterable<T>|T ...$items
     *
     * @throws ModelException When item type is not defined or invalid
     */
    public function __construct(iterable | ModelInterface ...$items)
    {
        if (! isset(static::$itemType)) {
            throw ModelException::undefinedItemType(static::class);
        }

        if (! is_a(static::$itemType, ModelInterface::class, true)) {
            throw ModelException::invalidItemType(static::$itemType);
        }

        $this->addItems(...$items);

        /** @psalm-suppress RedundantCondition */
        foreach ($items as $item) {
            if (!is_iterable($item) && !($item instanceof ModelInterface)) {
                throw ModelException::invalidItemType(get_debug_type($item));
            }
        }
    }

    /**
     * Add an item to the collection.
     *
     * @param T $item
     *
     * @throws ModelException When item type doesn't match the collection's item type
     */
    final public function add(ModelInterface $item): void
    {
        if (! $item instanceof static::$itemType) {
            throw ModelException::typeMismatch(static::$itemType, $item::class);
        }
        $this->models[] = $item;
    }

    /**
     * @return null|T
     */
    final public function current(): ?ModelInterface
    {
        $key = $this->key();
        if (null === $key) {
            return null;
        }

        return $this->models[$key] ?? null;
    }

    /**
     * Checks if all items match the callback.
     *
     * @param callable(T): bool $callback
     */
    final public function every(callable $callback): bool
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
    final public function filter(callable $callback): static
    {
        /** @var class-string<static> $collection */
        $collection = static::class;
        /** @var AbstractIndexedCollection<ModelInterface> $new */
        $new = new $collection();          // keep the same concrete class
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
    final public function first(?callable $callback = null): ?ModelInterface
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
     * @return array<int, array<string, mixed>>
     */
    final public function jsonSerialize(): array
    {
        return array_map(
            static fn (ModelInterface $item) => $item->jsonSerialize(),
            $this->models,
        );
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
    final public function map(string $targetType, callable $callback): static
    {
        if (! is_a($targetType, ModelInterface::class, true)) {
            throw ModelException::invalidItemType($targetType);
        }

        $new = new static();

        // Align the target collection’s expected item type
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

    /**
     * @param mixed $offset
     *
     * @return null|T
     */
    final public function offsetGet(mixed $offset): ?ModelInterface
    {
        return $this->models[$offset] ?? null;
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
    final public function reduce(mixed $initial, callable $callback): mixed
    {
        $result = $initial;
        foreach ($this->models as $item) {
            $result = $callback($result, $item);
        }

        return $result;
    }

    /**
     * Checks if any item matches the callback.
     *
     * @param callable(T): bool $callback
     */
    final public function some(callable $callback): bool
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
    final public function toArray(): array
    {
        return $this->models;
    }

    /**
     * @param iterable<T>|T ...$items
     *
     * @return static<T>
     */
    final public function withItems(iterable | ModelInterface ...$items): static
    {
        $new = clone $this;
        $new->addItems(...$items);

        return $new;
    }

    final public static function schema(): CollectionSchemaInterface
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

    /**
     * @param iterable<T>|T ...$items
     *
     * @throws ModelException
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
