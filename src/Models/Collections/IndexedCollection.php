<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use InvalidArgumentException;
use OpenFGA\Exceptions\SerializationError;
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
     * @var array<class-string, CollectionSchemaInterface>
     */
    private static array $cachedSchemas = [];

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

    /**
     * @inheritDoc
     */
    #[Override]
    public function add($item): static
    {
        if (! $item instanceof static::$itemType) {
            throw new TypeError(sprintf('Expected instance of %s, %s given', static::$itemType, $item::class));
        }

        $this->models[] = $item;

        return $this;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function clear(): void
    {
        $this->models = [];
        $this->position = 0;
    }

    /**
     * @inheritDoc
     *
     * @return int<0, max>
     */
    #[Override]
    public function count(): int
    {
        return count($this->models);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function current(): ModelInterface
    {
        $key = $this->key();

        return $this->models[$key];
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function every(callable $callback): bool
    {
        foreach ($this->models as $model) {
            if (! $callback($model)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    #[Override]
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

    /**
     * @inheritDoc
     */
    #[Override]
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

    /**
     * @inheritDoc
     */
    #[Override]
    public function get(int $offset)
    {
        return $this->models[$offset] ?? null;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function isEmpty(): bool
    {
        return [] === $this->models;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return array_map(
            static fn (ModelInterface $model): mixed => $model->jsonSerialize(),
            $this->models,
        );
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function key(): int
    {
        $key = array_keys($this->models)[$this->position] ?? null;

        if (! is_int($key)) {
            throw new OutOfBoundsException('Invalid position');
        }

        return $key;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function next(): void
    {
        ++$this->position;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->models[$offset]);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function offsetGet(mixed $offset): ?ModelInterface
    {
        return $this->models[$offset] ?? null;
    }

    /**
     * @inheritDoc
     */
    #[Override]
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

    /**
     * @inheritDoc
     */
    #[Override]
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
     * @inheritDoc
     */
    #[Override]
    public function reduce(mixed $initial, callable $callback): mixed
    {
        $result = $initial;
        foreach ($this->models as $model) {
            $result = $callback($result, $model);
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function rewind(): void
    {
        $this->position = 0;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function some(callable $callback): bool
    {
        foreach ($this->models as $model) {
            if ($callback($model)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function toArray(): array
    {
        return $this->models;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function valid(): bool
    {
        $keys = array_keys($this->models);

        return isset($keys[$this->position]);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function withItems(...$items): static
    {
        /** @var static<T> $new */
        /** @psalm-suppress UnnecessaryVarAnnotation */
        $new = clone $this;
        $new->addItems(...$items);

        return $new;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public static function schema(): CollectionSchemaInterface
    {
        if (isset(self::$cachedSchemas[static::class])) {
            return self::$cachedSchemas[static::class];
        }

        if (! isset(static::$itemType)) {
            throw SerializationError::UndefinedItemType->exception();
        }

        if (! is_a(static::$itemType, ModelInterface::class, true)) {
            throw SerializationError::InvalidItemType->exception();
        }

        $schema = new CollectionSchema(
            className: static::class,
            itemType: static::$itemType,
            requireItems: false,
        );

        self::$cachedSchemas[static::class] = $schema;

        return $schema;
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
