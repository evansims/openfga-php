<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\ComputedInterface;

/**
 * @template T of ComputedInterface
 *
 * @extends IndexedCollectionInterface<T>
 */
interface ComputedsInterface extends IndexedCollectionInterface
{
    /**
     * Add a computed to the collection.
     *
     * @param T $computed
     */
    public function add(ComputedInterface $computed): void;

    /**
     * Get the current computed in the collection.
     *
     * @return T
     */
    public function current(): ComputedInterface;

    /**
     * @return array<int, array{userset: string}>
     */
    public function jsonSerialize(): array;

    /**
     * Get a computed by offset.
     *
     * @param mixed $offset
     *
     * @return null|T
     */
    public function offsetGet(mixed $offset): ?ComputedInterface;
}
