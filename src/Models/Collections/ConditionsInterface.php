<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\ConditionInterface;

/**
 * @template T of ConditionInterface
 *
 * @extends IndexedCollectionInterface<T>
 */
interface ConditionsInterface extends IndexedCollectionInterface
{
    /**
     * Add a condition to the collection.
     *
     * @param T $condition
     */
    public function add(ConditionInterface $condition): void;

    /**
     * Get the current condition in the collection.
     *
     * @return T
     */
    public function current(): ConditionInterface;

    /**
     * @return array<int, array{name: string, expression: string, parameters?: array<string, mixed>, metadata?: array<string, mixed>}>
     */
    public function jsonSerialize(): array;

    /**
     * Get a condition by offset.
     *
     * @param mixed $offset
     *
     * @return null|T
     */
    public function offsetGet(mixed $offset): ?ConditionInterface;
}
