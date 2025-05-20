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
use function is_int;
use function is_object;
use function sprintf;

/**
 * @template T
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
     * @var class-string<T>
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

    /**
     * Add an item to the collection.
     *
     * @param T      $item
     * @param string $key
     */
    public function add(string $key, ModelInterface $item): void
    {
        if (! $item instanceof static::$itemType) {
            throw new TypeError(sprintf('Expected instance of %s, %s given', static::$itemType, $item::class));
        }

        $this->models[$key] = $item;
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
     * @param string $key
     *
     * @return null|T
     */
    public function get(string $key): ?ModelInterface
    {
        return $this->models[$key] ?? null;
    }

    /**
     * Check if a key exists in the collection.
     *
     * @param string $key
     */
    public function has(string $key): bool
    {
        return isset($this->models[$key]);
    }

    public function jsonSerialize(): array
    {
        $response = [];

        foreach ($this->models as $key => $model) {
            $response[$key] = $model->jsonSerialize();
        }

        return $response;
    }

    public function key(): string
    {
        $keys = array_keys($this->models);

        return $keys[$this->position] ?? throw new OutOfBoundsException('Invalid position');
    }

    /**
     * @template U
     *
     * @param callable(T): U $callback
     *
     * @return array<string, U>
     */
    public function map(callable $callback): array
    {
        $result = [];
        foreach ($this->models as $key => $item) {
            $result[$key] = $callback($item);
        }

        return $result;
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->models[$offset]);
    }

    public function offsetGet(mixed $offset): ?ModelInterface
    {
        return $this->models[$offset] ?? null;
    }

    /**
     * @param string $offset
     * @param T      $value
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (! $value instanceof static::$itemType) {
            throw new InvalidArgumentException(sprintf('Expected instance of %s, %s given.', static::$itemType, is_object($value) ? $value::class : gettype($value)));
        }

        if (null === $offset) {
            throw new InvalidArgumentException('KeyedCollection requires an explicit string key.');
        }

        $this->models[$offset] = $value;
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

    /**
     * @return array<string, T>
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
