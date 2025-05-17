<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class UsersList extends AbstractIndexedCollection implements UsersListInterface
{
    /**
     * @var class-string<UsersListUserInterface>
     */
    protected static string $itemType = UsersListUser::class;

    /**
     * @param iterable<UsersListUserInterface>|UsersListUserInterface ...$users
     */
    public function __construct(iterable | UsersListUserInterface ...$users)
    {
        parent::__construct(...$users);
    }
}
