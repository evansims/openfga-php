<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @psalm-type AssertionsShape = list<AssertionShape>
 */
interface AssertionsInterface extends CollectionInterface
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
     * @return AssertionsShape
     */
    public function jsonSerialize(): array;

    /**
     * Get an assertion by offset.
     *
     * @param mixed $offset
     *
     * @return null|AssertionInterface
     */
    public function offsetGet(mixed $offset): ?AssertionInterface;

    /**
     * @param AssertionsShape $data
     */
    public static function fromArray(array $data): static;
}
