<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class Usersets implements UsersetsInterface
{
    use CollectionTrait;

    public function add(UsersetInterface $userset): void
    {
        $this->models[] = $userset;
    }

    public function current(): UsersetInterface
    {
        return $this->models[$this->key()];
    }

    public function offsetGet(mixed $offset): ?UsersetInterface
    {
        return $this->models[$offset] ?? null;
    }

    public static function fromArray(array $data): self
    {
        $data = self::validatedUsersetsShape($data);
        $collection = new self();

        foreach ($data as $model) {
            $collection->add(Userset::fromArray($model));
        }

        return $collection;
    }

    /**
     * Validates the shape of the array to be used as usersets data. Throws an exception if the data is invalid.
     *
     * @param array<int,UsersetShape> $data
     *
     * @throws InvalidArgumentException
     *
     * @return UsersetsShape
     */
    public static function validatedUsersetsShape(array $data): array
    {
        return $data;
    }
}
