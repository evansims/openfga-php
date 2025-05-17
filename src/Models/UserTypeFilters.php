<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @extends AbstractIndexedCollection<UserTypeFilter>
 */
final class UserTypeFilters extends AbstractIndexedCollection implements UserTypeFiltersInterface
{
    protected static string $itemType = UserTypeFilter::class;

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
        $result = parent::offsetGet($offset);

        return $result instanceof UserTypeFilterInterface ? $result : null;
    }
}
