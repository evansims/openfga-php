<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface AssertionsInterface extends ModelCollectionInterface
{
    /**
     * Add an assertion to the collection.
     *
     * @param AssertionInterface $assertion
     */
    public function add(AssertionInterface $assertion): void;

    /**
     * Get the current assertion in the collection.
     *
     * @return AssertionInterface
     */
    public function current(): AssertionInterface;

    /**
     * Get an assertion by offset.
     *
     * @param mixed $offset
     *
     * @return null|AssertionInterface
     */
    public function offsetGet(mixed $offset): ?AssertionInterface;
}
