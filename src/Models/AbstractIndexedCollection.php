<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use ArrayAccess;
use Countable;
use InvalidArgumentException;
use Iterator;
use JsonSerializable;
use OpenFGA\Schema\{CollectionSchema, CollectionSchemaInterface};
use RuntimeException;

use function sprintf;

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
     * @param T ...$items
     */
    public function __construct(
        ModelInterface ...$items,
    ) {
        foreach ($items as $item) {
            $this->add($item);
        }
    }

    /**
     * Add an item to the collection.
     *
     * @param T $item
     */
    final public function add(ModelInterface $item): void
    {
        $this->assertItemType($item, static::$itemType);
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
     * @param mixed $offset
     *
     * @return null|T
     */
    final public function offsetGet(mixed $offset): ?ModelInterface
    {
        return $this->models[$offset] ?? null;
    }

    final public static function schema(): CollectionSchemaInterface
    {
        if (null === static::$schema) {
            if (! isset(static::$itemType)) {
                throw new RuntimeException('Child class must define static property $itemType');
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
     * Asserts that the given item is of the correct type for this collection.
     *
     * @param ModelInterface $item         The item to check
     * @param string         $expectedType The expected type (class/interface name)
     *
     * @throws InvalidArgumentException If the item is not of the expected type
     */
    protected function assertItemType(ModelInterface $item, string $expectedType): void
    {
        if (! is_a($item, $expectedType)) {
            throw new InvalidArgumentException(sprintf('Expected instance of %s, got %s', $expectedType, get_debug_type($item)));
        }
    }
}
