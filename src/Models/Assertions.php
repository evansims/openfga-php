<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @extends AbstractIndexedCollection<Assertion>
 */
final class Assertions extends AbstractIndexedCollection implements AssertionsInterface
{
    protected static string $itemType = Assertion::class;

    /**
     * @return null|AssertionInterface
     */
    public function current(): ?AssertionInterface
    {
        /** @var null|AssertionInterface $result */
        return parent::current();
    }

    /**
     * @param mixed $offset
     *
     * @return null|AssertionInterface
     */
    public function offsetGet(mixed $offset): ?AssertionInterface
    {
        /** @var null|AssertionInterface $result */
        $result = parent::offsetGet($offset);

        return $result instanceof AssertionInterface ? $result : null;
    }
}
