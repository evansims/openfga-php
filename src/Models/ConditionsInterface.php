<?php

namespace OpenFGA\Models;

interface ConditionsInterface extends ModelCollectionInterface
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
     * @return ConditionInterface
     */
    public function current(): ConditionInterface;

    /**
     * Get a condition by offset.
     *
     * @param mixed $offset
     *
     * @return ConditionInterface|null
     */
    public function offsetGet(mixed $offset): ?ConditionInterface;
}
