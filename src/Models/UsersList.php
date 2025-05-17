<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @template T of UsersListUserInterface
 * @extends AbstractIndexedCollection<T>
 */
final class UsersList extends AbstractIndexedCollection implements UsersListInterface
{
    /**
     * @var class-string<T>
     */
    protected static string $itemType = UsersListUser::class;

    /**
     * @param list<T>|T ...$users
     */
    public function __construct(iterable | UsersListUserInterface ...$users)
    {
        parent::__construct(...$users);
    }
}
