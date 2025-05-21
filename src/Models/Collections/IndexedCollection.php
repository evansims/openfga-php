<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use InvalidArgumentException;
use OpenFGA\Exceptions\ModelException;
use OpenFGA\Models\ModelInterface;
use OpenFGA\Schema\{CollectionSchema, CollectionSchemaInterface};
use OutOfBoundsException;
use Override;

use TypeError;

use function count;
use function is_a;
use function is_int;
use function is_iterable;
use function sprintf;

/**
 * @template T of ModelInterface
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
     * @phpstan-var class-string<ModelInterface>
     *
     * @var class-string<ModelInterface>
     */
    protected static string $itemType;

    /**
     * @param iterable<T>|T ...$items
     */
    final public function __construct(...$items)
    {
        if (! isset(static::$itemType)) {
            throw new TypeError(sprintf('Undefined item type for %s. Define the $itemType property or override the constructor.', static::class));
        }

        if (! is_a(static::$itemType, ModelInterface::class, true)) {
            throw new TypeError(sprintf('Expected item type to implement %s, %s given', ModelInterface::class, static::$itemType));
        }

        foreach ($this->normalizeItems($items) as $item) {
            $this->add($item);
        }
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function add($item): static
    {
        if (! $item instanceof static::$itemType) {
            throw new TypeError(sprintf('Expected instance of %s, %s given', static::$itemType, $item::class));
        }

        $this->models[] = $item;

        return $this;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function clear(): void
    {
        $this->models = [];
        $this->position = 0;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->models);
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function current(): ModelInterface
    {
        $key = $this->key();

        return $this->models[$key];
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function every(callable $callback): bool
    {
        foreach ($this->models as $model) {
            if (! $callback($model)) {
                return false;
            }
        }

        return true;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function filter(callable $callback): static
    {
        /** @var static<T> $new */
        $new = new static();

        foreach ($this->models as $model) {
            if ($callback($model)) {
                $new->add($model);
            }
        }

        return $new;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function first(?callable $callback = null)
    {
        if (null === $callback) {
            return $this->models[0] ?? null;
        }

        foreach ($this->models as $model) {
            if ($callback($model)) {
                return $model;
            }
        }

        return null;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function get(int $offset)
    {
        return $this->models[$offset] ?? null;
    }

    public function isEmpty(): bool
    {
        return [] === $this->models;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return array_map(
            static fn (ModelInterface $model): mixed => $model->jsonSerialize(),
            $this->models,
        );
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function key(): int
    {
        $key = array_keys($this->models)[$this->position] ?? null;

        if (! is_int($key)) {
            throw new OutOfBoundsException('Invalid position');
        }

        return $key;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function next(): void
    {
        ++$this->position;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->models[$offset]);
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function offsetGet(mixed $offset): ?ModelInterface
    {
        return $this->models[$offset] ?? null;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (! $value instanceof static::$itemType) {
            throw new InvalidArgumentException(sprintf('Expected instance of %s, %s given.', static::$itemType, get_debug_type($value)));
        }

        if (null === $offset) {
            $this->models[] = $value;
        } else {
            $this->models[$offset] = $value;
        }
    }

    #[Override]
    /**
     * @inheritDoc
     */
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

    #[Override]
    /**
     * @inheritDoc
     */
    public function reduce(mixed $initial, callable $callback): mixed
    {
        $result = $initial;
        foreach ($this->models as $model) {
            $result = $callback($result, $model);
        }

        return $result;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function rewind(): void
    {
        $this->position = 0;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function some(callable $callback): bool
    {
        foreach ($this->models as $model) {
            if ($callback($model)) {
                return true;
            }
        }

        return false;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return $this->models;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function valid(): bool
    {
        $keys = array_keys($this->models);

        return isset($keys[$this->position]);
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function withItems(...$items): static
    {
        /** @var static<T> $new */
        /** @psalm-suppress UnnecessaryVarAnnotation */
        $new = clone $this;
        $new->addItems(...$items);

        return $new;
    }

    #[Override]
    /**
     * @inheritDoc
     */
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
    protected function addItems(...$items): void
    {
        foreach ($this->normalizeItems($items) as $item) {
            $this->add($item);
        }
    }

    /**
     * @param array<int|string, iterable<T>|T> $items
     *
     * @return iterable<T>
     */
    protected function normalizeItems(array $items): iterable
    {
        foreach ($items as $item) {
            if (is_iterable($item)) {
                foreach ($item as $i) {
                    /** @var T $i */
                    yield $i;
                }
            } else {
                yield $item;
            }
        }
    }
}
