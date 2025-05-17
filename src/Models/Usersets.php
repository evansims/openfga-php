<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @template T of UsersetInterface
 * @extends AbstractIndexedCollection<T>
 */
final class Usersets extends AbstractIndexedCollection implements UsersetsInterface
{
    /**
     * @var class-string<T>
     */
    protected static string $itemType = Userset::class;

    /**
     * @param list<T>|T ...$usersets
     */
    public function __construct(iterable|UsersetInterface ...$usersets)
    {
        parent::__construct(...$usersets);
    }
}
