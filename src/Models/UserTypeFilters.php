<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @extends AbstractIndexedCollection<UserTypeFilter>
 */
final class UserTypeFilters extends AbstractIndexedCollection implements UserTypeFiltersInterface
{
    protected static string $itemType = UserTypeFilter::class;
}
