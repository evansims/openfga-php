<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface ConditionParametersInterface extends ModelCollectionInterface
{
    /**
     * Add a condition to the collection.
     *
     * @param ConditionParameterInterface $condition
     */
    public function add(ConditionParameterInterface $condition): void;

    /**
     * Get the current condition in the collection.
     *
     * @return ConditionParameterInterface
     */
    public function current(): ConditionParameterInterface;

    /**
     * Get a condition by offset.
     *
     * @param mixed $offset
     *
     * @return null|ConditionParameterInterface
     */
    public function offsetGet(mixed $offset): ?ConditionParameterInterface;
}
