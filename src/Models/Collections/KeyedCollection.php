<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use InvalidArgumentException;
use OpenFGA\Exceptions\ModelException;
use OpenFGA\Models\ModelInterface;
use OpenFGA\Schema\{CollectionSchema, CollectionSchemaInterface};
use OutOfBoundsException;
use Override;

use ReturnTypeWillChange;

use TypeError;

use function count;
use function is_int;
use function is_string;
use function sprintf;

/**
 * @template T of ModelInterface
 *
 * @implements KeyedCollectionInterface<T>
 */
abstract class KeyedCollection implements KeyedCollectionInterface
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
     * @param array<string, T> $items
     *
     * @throws TypeError When item type is not defined or invalid
     */
    public function __construct(array $items)
    {
        if (! isset(static::$itemType)) {
            throw new TypeError(sprintf('Undefined item type for %s. Define the $itemType property or override the constructor.', static::class));
        }

        if (! is_a(static::$itemType, ModelInterface::class, true)) {
            throw new TypeError(sprintf('Expected item type to implement %s, %s given', ModelInterface::class, static::$itemType));
        }

        $isAssoc = ! array_is_list($items);

        if ($isAssoc) {
            // For associative arrays, use the provided keys
            foreach ($items as $key => $item) {
                $this->add($key, $item);
            }
        } else {
            // For numeric arrays, use numeric indices as strings
            foreach ($items as $index => $item) {
                $this->add((string) $index, $item);
            }
        }
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function add(string $key, ModelInterface $item): static
    {
        if (! $item instanceof static::$itemType) {
            throw new TypeError(sprintf('Expected instance of %s, %s given', static::$itemType, $item::class));
        }

        $this->models[$key] = $item;

        return $this;
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
    #[ReturnTypeWillChange]
    public function current(): ModelInterface
    {
        $key = $this->key();

        return $this->models[$key];
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function get(string $key)
    {
        return $this->models[$key] ?? null;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function has(string $key): bool
    {
        return isset($this->models[$key]);
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        $result = [];

        foreach ($this->models as $key => $model) {
            /** @var T $model */
            $result[$key] = $model->jsonSerialize();
        }

        /** @var array<string, mixed> $result */
        return $result;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function key(): string
    {
        $key = array_keys($this->models)[$this->position] ?? null;

        if (! is_string($key)) {
            throw new OutOfBoundsException('Invalid key type; expected string.');
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
    #[ReturnTypeWillChange]
    public function offsetGet(mixed $offset)
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

        if (! is_string($offset)) {
            throw new InvalidArgumentException('Key must be a string.');
        }

        $this->models[$offset] = $value;
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
    public function rewind(): void
    {
        $this->position = 0;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        $copy = [];

        foreach ($this->models as $key => $value) {
            if (! is_string($key)) {
                continue; // or throw if you want stricter enforcement
            }

            /** @var T $value */
            $copy[$key] = $value;
        }

        return $copy;
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
}
