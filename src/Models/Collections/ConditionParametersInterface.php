<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\ConditionParameterInterface;

/**
 * @template T of ConditionParameterInterface
 *
 * @extends IndexedCollectionInterface<T>
 */
interface ConditionParametersInterface extends IndexedCollectionInterface
{
    /**
     * Add a condition to the collection.
     *
     * @param T $condition
     */
    public function add(ConditionParameterInterface $condition): void;

    /**
     * Get the current condition in the collection.
     *
     * @return T
     */
    public function current(): ConditionParameterInterface;

    /**
     * @return list<array{type_name: string, generic_types?: array<int, mixed>}>
     */
    public function jsonSerialize(): array;

    /**
     * Get a condition by offset.
     *
     * @param mixed $offset
     *
     * @return null|T
     */
    public function offsetGet(mixed $offset): ?ConditionParameterInterface;
}
