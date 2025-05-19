<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schema\CollectionSchemaInterface;

/**
 * @extends IndexedCollection<User>
 */
final class Users extends IndexedCollection implements UsersInterface
{
    protected static string $itemType = User::class;

    protected static ?CollectionSchemaInterface $schema = null;

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
