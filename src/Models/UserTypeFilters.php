<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @template T of UserTypeFilterInterface
 * @extends AbstractIndexedCollection<T>
 */
final class UserTypeFilters extends AbstractIndexedCollection implements UserTypeFiltersInterface
{
    /**
     * @var class-string<T>
     */
    protected static string $itemType = UserTypeFilter::class;

    /**
     * @param list<T>|T ...$userTypeFilters
     */
    public function __construct(iterable | UserTypeFilterInterface ...$userTypeFilters)
    {
        parent::__construct(...$userTypeFilters);
    }
}
