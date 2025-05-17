<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class Usersets extends AbstractIndexedCollection implements UsersetsInterface
{
    /**
     * @var class-string<UsersetInterface>
     */
    protected static string $itemType = Userset::class;

    /**
     * @param iterable<UsersetInterface>|UsersetInterface ...$usersets
     */
    public function __construct(iterable | UsersetInterface ...$usersets)
    {
        parent::__construct(...$usersets);
    }
}
