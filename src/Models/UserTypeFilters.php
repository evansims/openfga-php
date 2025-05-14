<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class UserTypeFilters implements UserTypeFiltersInterface
{
    use CollectionTrait;

    public function add(UserTypeFilterInterface $userTypeFilter): void
    {
        $this->models[] = $userTypeFilter;
    }

    public function current(): UserTypeFilterInterface
    {
        return $this->models[$this->key()];
    }

    public function offsetGet(mixed $offset): ?UserTypeFilterInterface
    {
        return $this->models[$offset] ?? null;
    }

    public static function fromArray(array $data): self
    {
        $data = self::validatedUserTypeFiltersShape($data);
        $collection = new self();

        foreach ($data as $model) {
            $collection->add(UserTypeFilter::fromArray($model));
        }

        return $collection;
    }

    /**
     * @param list<UserTypeFilterShape> $data
     *
     * @return UserTypeFiltersShape
     */
    public static function validatedUserTypeFiltersShape(array $data): array
    {
        return $data;
    }
}
