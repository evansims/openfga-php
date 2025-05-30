<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use Generator;
use InvalidArgumentException;
use OpenFGA\Exceptions\{ClientError, ClientThrowable, SerializationError};
use OpenFGA\Messages;
use OpenFGA\Models\ModelInterface;
use OpenFGA\Schema\{CollectionSchema, CollectionSchemaInterface};
use OpenFGA\Translation\Translator;
use Override;
use ReflectionClass;
use ReflectionException;

use function count;
use function is_a;
use function is_int;
use function is_iterable;
use function is_string;

/**
 * Base implementation for integer-indexed collections in the OpenFGA SDK.
 *
 * This abstract class provides a foundation for collections that are indexed
 * by integers, similar to JSON arrays. It includes validation, iteration,
 * and manipulation methods while ensuring type safety for contained items.
 *
 * Collections extending this class can hold any type of model that implements
 * ModelInterface, with runtime type checking to ensure data integrity.
 *
 * @template T of ModelInterface
 *
 * @implements IndexedCollectionInterface<T>
 */
abstract class IndexedCollection implements IndexedCollectionInterface
{
    /**
     * @phpstan-var class-string<T>
     *
     * @psalm-var class-string<ModelInterface>
     *
     * @var class-string<ModelInterface>
     */
    protected static string $itemType;

    /**
     * @var array<class-string, CollectionSchemaInterface>
     */
    private static array $cachedSchemas = [];

    /**
     * @var array<int|string, T>
     */
    private array $models = [];

    private int $position = 0;

    /**
     * @param iterable<T>|T ...$items
     *
     * @throws ClientThrowable          When validation fails
     * @throws InvalidArgumentException If parameters are invalid or message translation fails
     * @throws ReflectionException      If exception location capture fails
     */
    final public function __construct(...$items)
    {
        $reflection = new ReflectionClass(static::class);
        if (! $reflection->hasProperty('itemType')) {
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::COLLECTION_UNDEFINED_ITEM_TYPE, ['class' => static::class])]);
        }

        $property = $reflection->getProperty('itemType');
        if (! $property->isInitialized()) {
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::COLLECTION_UNDEFINED_ITEM_TYPE, ['class' => static::class])]);
        }

        if (! is_a(static::$itemType, ModelInterface::class, true)) {
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::COLLECTION_INVALID_ITEM_TYPE_INTERFACE, ['interface' => ModelInterface::class, 'given' => static::$itemType])]);
        }

        foreach ($this->normalizeItems($items) as $item) {
            $this->add($item);
        }
    }

    /**
     * @inheritDoc
     *
     * @throws ClientThrowable          If item type is not defined or invalid
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    #[Override]
    public static function schema(): CollectionSchemaInterface
    {
        if (isset(self::$cachedSchemas[static::class])) {
            return self::$cachedSchemas[static::class];
        }

        $reflection = new ReflectionClass(static::class);
        if (! $reflection->hasProperty('itemType')) {
            throw SerializationError::UndefinedItemType->exception();
        }

        $property = $reflection->getProperty('itemType');
        if (! $property->isInitialized()) {
            throw SerializationError::UndefinedItemType->exception();
        }

        if (! is_a(static::$itemType, ModelInterface::class, true)) {
            throw SerializationError::InvalidItemType->exception();
        }

        $itemTypeString = static::$itemType;

        $schema = new CollectionSchema(
            className: static::class,
            itemType: $itemTypeString,
            requireItems: false,
        );

        self::$cachedSchemas[static::class] = $schema;

        return $schema;
    }

    /**
     * @inheritDoc
     *
     * @throws ClientThrowable          If the item is not an instance of the expected type
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    #[Override]
    public function add($item): static
    {
        if (! $item instanceof static::$itemType) {
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::COLLECTION_INVALID_ITEM_INSTANCE, ['expected' => static::$itemType, 'given' => $item::class])]);
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
     *
     * @throws ClientThrowable          If the current position key is not an integer
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
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
     *
     * @throws ClientThrowable          If the filtered items fail validation during construction
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     *
     * @psalm-suppress UnsafeGenericInstantiation
     */
    #[Override]
    public function filter(callable $callback): static
    {
        /** @var static<T> $new */
        $new = new static;

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
     *
     * @throws ClientThrowable          If the current position key is not an integer
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    #[Override]
    public function key(): int
    {
        $key = array_keys($this->models)[$this->position] ?? null;

        if (! is_int($key)) {
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::COLLECTION_INVALID_POSITION)]);
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
        if (! is_int($offset) && ! is_string($offset)) {
            return false;
        }

        return isset($this->models[$offset]);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function offsetGet(mixed $offset): ?ModelInterface
    {
        if (! is_int($offset) && ! is_string($offset)) {
            return null;
        }

        return $this->models[$offset] ?? null;
    }

    /**
     * @inheritDoc
     *
     * @throws ClientThrowable          If the value is not an instance of the expected type
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    #[Override]
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (! $value instanceof static::$itemType) {
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::COLLECTION_INVALID_VALUE_TYPE, ['expected' => static::$itemType, 'given' => get_debug_type($value)])]);
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
        if ((is_int($offset) || is_string($offset)) && isset($this->models[$offset])) {
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
     *
     * @throws ClientThrowable          If any items fail validation during addition
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    #[Override]
    public function withItems(...$items): static
    {
        $new = clone $this;
        $new->addItems(...$items);

        return $new;
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
     * @psalm-return Generator<int, T, mixed, void>
     */
    protected function normalizeItems(array $items): Generator
    {
        foreach ($items as $item) {
            if (is_iterable($item)) {
                /** @var iterable<T> $item */
                foreach ($item as $i) {
                    yield $i;
                }
            } else {
                yield $item;
            }
        }
    }
}
