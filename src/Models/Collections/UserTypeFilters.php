<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\{UserTypeFilter, UserTypeFilterInterface};

/**
 * @extends IndexedCollection<UserTypeFilterInterface>
 *
 * @implements UserTypeFiltersInterface<UserTypeFilterInterface>
 */
final class UserTypeFilters extends IndexedCollection implements UserTypeFiltersInterface
{
    protected static string $itemType = UserTypeFilter::class;
}
