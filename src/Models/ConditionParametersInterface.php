<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @psalm-type ConditionParametersShape = list<ConditionParameterShape>
 */
interface ConditionParametersInterface extends IndexedCollectionInterface
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
     * @return null|ConditionParameterInterface
     */
    public function current(): ?ConditionParameterInterface;

    /**
     * @return ConditionParametersShape
     */
    public function jsonSerialize(): array;

    /**
     * Get a condition by offset.
     *
     * @param mixed $offset
     *
     * @return null|ConditionParameterInterface
     */
    public function offsetGet(mixed $offset): ?ConditionParameterInterface;
}
