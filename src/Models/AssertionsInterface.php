<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @template T of Assertion
 *
 * @extends IndexedCollectionInterface<T>
 *
 * @psalm-type AssertionsShape = list<AssertionShape>
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
     * @return AssertionsShape
     */
    public function jsonSerialize(): array;
}
