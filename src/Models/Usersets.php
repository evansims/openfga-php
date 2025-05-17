<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @extends AbstractIndexedCollection<Userset>
 */
final class Usersets extends AbstractIndexedCollection implements UsersetsInterface
{
    protected static string $itemType = Userset::class;

    /**
     * @return null|UsersetInterface
     */
    public function current(): ?UsersetInterface
    {
        /** @var null|UsersetInterface $result */
        return parent::current();
    }

    /**
     * @param mixed $offset
     *
     * @return null|UsersetInterface
     */
    public function offsetGet(mixed $offset): ?UsersetInterface
    {
        /** @var null|UsersetInterface $result */
        $result = parent::offsetGet($offset);

        return $result instanceof UsersetInterface ? $result : null;
    }
}
