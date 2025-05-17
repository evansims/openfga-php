<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @psalm-type ConditionsShape = list<ConditionShape>
 *
 * @extends IndexedCollectionInterface<ConditionInterface>
 *
 * @implements \ArrayAccess<int, ConditionInterface>
 * @implements \Iterator<int, ConditionInterface>
 */
interface ConditionsInterface extends IndexedCollectionInterface
{
    /**
     * Add a condition to the collection.
     *
     * @param ConditionInterface $condition
     */
    public function add(ConditionInterface $condition): void;

    /**
     * Get the current condition in the collection.
     *
     * @return null|ConditionInterface
     */
    public function current(): ?ConditionInterface;

    /**
     * @return ConditionsShape
     */
    public function jsonSerialize(): array;

    /**
     * Get a condition by offset.
     *
     * @param mixed $offset
     *
     * @return null|ConditionInterface
     */
    public function offsetGet(mixed $offset): ?ConditionInterface;
}
