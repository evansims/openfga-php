<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @template T of UserInterface
 * @extends AbstractIndexedCollection<T>
 */
final class Users extends AbstractIndexedCollection implements UsersInterface
{
    /**
     * @var class-string<T>
     */
    protected static string $itemType = User::class;

    /**
     * @param list<T>|T ...$users
     */
    public function __construct(iterable | UserInterface ...$users)
    {
        parent::__construct(...$users);
    }
}
