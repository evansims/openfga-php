<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @implements \ArrayAccess<int, UserTypeFilterInterface>
 * @implements \Iterator<int, UserTypeFilterInterface>
 */
final class UserTypeFilters extends AbstractIndexedCollection implements UserTypeFiltersInterface
{
    /**
     * @var class-string<UserTypeFilterInterface>
     */
    protected static string $itemType = UserTypeFilter::class;

    /**
     * @param iterable<UserTypeFilterInterface>|UserTypeFilterInterface ...$userTypeFilters
     */
    public function __construct(iterable | UserTypeFilterInterface ...$userTypeFilters)
    {
        parent::__construct(...$userTypeFilters);
    }
}
