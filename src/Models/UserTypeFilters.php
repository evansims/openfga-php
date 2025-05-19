<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schema\CollectionSchemaInterface;

/**
 * @extends IndexedCollection<UserTypeFilter>
 *
 * @implements UserTypeFiltersInterface<UserTypeFilter>
 */
final class UserTypeFilters extends IndexedCollection implements UserTypeFiltersInterface
{
    protected static string $itemType = UserTypeFilter::class;

    protected static ?CollectionSchemaInterface $schema = null;

    /**
     * @return null|UserTypeFilterInterface
     */
    public function current(): ?UserTypeFilterInterface
    {
        /** @var null|UserTypeFilterInterface $result */
        return parent::current();
    }

    /**
     * @param mixed $offset
     *
     * @return null|UserTypeFilterInterface
     */
    public function offsetGet(mixed $offset): ?UserTypeFilterInterface
    {
        /** @var null|UserTypeFilterInterface $result */
        return parent::offsetGet($offset);
    }
}
