<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\{ModelInterface, UserTypeFilter, UserTypeFilterInterface};

/**
 * @extends IndexedCollection<UserTypeFilterInterface>
 *
 * @implements UserTypeFiltersInterface<UserTypeFilterInterface>
 */
final class UserTypeFilters extends IndexedCollection implements UserTypeFiltersInterface
{
    /**
     * @phpstan-var class-string<UserTypeFilterInterface>
     *
     * @psalm-var class-string<ModelInterface>
     *
     * @var class-string<ModelInterface>
     */
    protected static string $itemType = UserTypeFilter::class;
}
