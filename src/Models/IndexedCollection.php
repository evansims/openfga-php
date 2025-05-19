<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use ReturnTypeWillChange;
use TypeError;
use OpenFGA\Exceptions\ModelException;

use function is_a;
use function is_iterable;
use function sprintf;

/**
 * Generic indexed collection implementation without traits or abstract classes.
 *
 * @template T of ModelInterface
 *
 * @extends Collection<T>
 *
 * @implements IndexedCollectionInterface<T>
 */
class IndexedCollection extends Collection implements IndexedCollectionInterface
{
    /**
     * @param iterable<T>|T ...$items
     *
     * @throws TypeError When item type is not defined or invalid
     */
    public function __construct(iterable | ModelInterface ...$items)
    {
        if (! isset(self::$itemType)) {
            throw new TypeError(sprintf('Undefined item type for %s. Define the $itemType property or override the constructor.', self::class));
        }

        if (! is_a(self::$itemType, ModelInterface::class, true)) {
            throw new TypeError(sprintf('Expected item type to implement %s, %s given', ModelInterface::class, self::$itemType));
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
     * Add an item to the collection.
     *
     * @param T $item
     */
    public function add(ModelInterface $item): void
    {
        if (! $item instanceof self::$itemType) {
            throw new TypeError(sprintf('Expected instance of %s, %s given', self::$itemType, $item::class));
        }
        $this->models[] = $item;
    }

    /**
     * @return null|T
     */
    #[ReturnTypeWillChange]
    public function current(): mixed
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
        $collection = self::class;
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

    /**
     * @param mixed $offset
     *
     * @return null|T
     */
    #[ReturnTypeWillChange]
    public function offsetGet(mixed $offset): mixed
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
    public function reduce(mixed $initial, callable $callback): mixed
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
