<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @extends AbstractIndexedCollection<User>
 */
final class Users extends AbstractIndexedCollection implements UsersInterface
{
    protected static string $itemType = User::class;

    /**
     * @return null|UserInterface
     */
    public function current(): ?UserInterface
    {
        /** @var null|UserInterface $result */
        return parent::current();
    }

    /**
     * @param mixed $offset
     *
     * @return null|UserInterface
     */
    public function offsetGet(mixed $offset): ?UserInterface
    {
        /** @var null|UserInterface $result */
        $result = parent::offsetGet($offset);

        return $result instanceof UserInterface ? $result : null;
    }
}
