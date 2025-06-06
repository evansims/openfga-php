<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use InvalidArgumentException;
use OpenFGA\Exceptions\{ClientError, ClientThrowable, SerializationError};
use OpenFGA\Messages;
use OpenFGA\Models\ModelInterface;
use OpenFGA\Schemas\{CollectionSchema, CollectionSchemaInterface};
use OpenFGA\Translation\Translator;
use Override;
use ReflectionClass;
use ReflectionException;
use ReturnTypeWillChange;

use function count;
use function is_string;

/**
 * Base implementation for string-keyed collections in the OpenFGA SDK.
 *
 * This abstract class provides a foundation for collections that are indexed
 * by strings, similar to JSON objects or associative arrays. It includes
 * validation, iteration, and manipulation methods while ensuring type safety.
 *
 * Collections extending this class can hold any type of model that implements
 * ModelInterface, with runtime type checking to ensure data integrity and
 * proper key-value associations.
 *
 * @template T of ModelInterface
 *
 * @implements KeyedCollectionInterface<T>
 */
abstract class KeyedCollection implements KeyedCollectionInterface
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
     * @param array<int|string, T> $items
     *
     * @throws ClientThrowable          When item type is not defined or invalid
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    public function __construct(array $items)
    {
        $reflection = new ReflectionClass(static::class);
        $property = $reflection->getProperty('itemType');

        if (! $property->isInitialized()) {
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::COLLECTION_UNDEFINED_ITEM_TYPE, ['class' => static::class])]);
        }

        if (! is_a(static::$itemType, ModelInterface::class, true)) {
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::COLLECTION_INVALID_ITEM_TYPE_INTERFACE, ['interface' => ModelInterface::class, 'given' => static::$itemType])]);
        }

        $isAssoc = ! array_is_list($items);

        if ($isAssoc) {
            // For associative arrays, use the provided keys
            foreach ($items as $key => $item) {
                $this->add((string) $key, $item);
            }
        } else {
            // For numeric arrays, use numeric indices as strings
            foreach ($items as $index => $item) {
                $this->add((string) $index, $item);
            }
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
        $property = $reflection->getProperty('itemType');

        if (! $property->isInitialized()) {
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
     * @inheritDoc
     *
     * @throws ClientThrowable          If the item is not an instance of the expected type
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    #[Override]
    public function add(string $key, ModelInterface $item): static
    {
        if (! $item instanceof static::$itemType) {
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::COLLECTION_INVALID_ITEM_INSTANCE, ['expected' => static::$itemType, 'given' => $item::class])]);
        }

        $this->models[$key] = $item;

        return $this;
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
     * @throws ClientThrowable          If the current key is not a string
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    #[Override]
    #[ReturnTypeWillChange]
    public function current(): ModelInterface
    {
        $key = $this->key();

        return $this->models[$key];
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function get(string $key)
    {
        return $this->models[$key] ?? null;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function has(string $key): bool
    {
        return isset($this->models[$key]);
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
     *
     * @return array<string, mixed>
     */
    #[Override]
    public function jsonSerialize(): array
    {
        /** @var array<string, mixed> $result */
        $result = [];

        foreach ($this->models as $key => $model) {
            if (is_string($key)) {
                /** @var array<string, mixed> $serialized */
                $serialized = $model->jsonSerialize();
                $result[$key] = $serialized;
            }
        }

        return $result;
    }

    /**
     * @inheritDoc
     *
     * @throws ClientThrowable          If the current key is not a string
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    #[Override]
    public function key(): string
    {
        $key = array_keys($this->models)[$this->position] ?? null;

        if (! is_string($key)) {
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::COLLECTION_INVALID_KEY_TYPE, ['given' => get_debug_type($key)])]);
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
        if (! is_string($offset)) {
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
        if (! is_string($offset)) {
            return null;
        }

        return $this->models[$offset] ?? null;
    }

    /**
     * @inheritDoc
     *
     * @throws ClientThrowable          If the value is not an instance of the expected type or offset is not a string
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    #[Override]
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (! $value instanceof static::$itemType) {
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::COLLECTION_INVALID_VALUE_TYPE, ['expected' => static::$itemType, 'given' => get_debug_type($value)])]);
        }

        if (! is_string($offset)) {
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::COLLECTION_KEY_MUST_BE_STRING)]);
        }

        $this->models[$offset] = $value;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function offsetUnset(mixed $offset): void
    {
        if (is_string($offset) && isset($this->models[$offset])) {
            unset($this->models[$offset]);
        }
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
    public function toArray(): array
    {
        $copy = [];

        foreach ($this->models as $key => $value) {
            if (! is_string($key)) {
                continue;
            }

            $copy[$key] = $value;
        }

        return $copy;
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
}
