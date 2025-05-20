<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\AssertionInterface;

/**
 * @template T of AssertionInterface
 *
 * @extends IndexedCollectionInterface<T>
 */
interface AssertionsInterface extends IndexedCollectionInterface
{
    /**
     * Add an assertion to the collection.
     *
     * @param T $assertion
     */
    public function add(AssertionInterface $assertion): void;

    /**
     * Get the current assertion in the collection.
     *
     * @return T
     */
    public function current(): AssertionInterface;

    /**
     * @return array<int, array{
     *     tuple_key: array<string, mixed>,
     *     expectation: bool,
     *     contextual_tuples?: array<array-key, mixed>,
     *     context?: array<array-key, mixed>
     * }>
     */
    public function jsonSerialize(): array;

    /**
     * Get an assertion by offset.
     *
     * @param mixed $offset
     *
     * @return null|T
     */
    public function offsetGet(mixed $offset): ?AssertionInterface;
}
