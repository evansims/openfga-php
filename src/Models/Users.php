<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class Users implements UsersInterface
{
    use CollectionTrait;

    public function add(UserInterface $user): void
    {
        $this->models[] = $user;
    }

    public function current(): UserInterface
    {
        return $this->models[$this->key()];
    }

    public function offsetGet(mixed $offset): ?UserInterface
    {
        return $this->models[$offset] ?? null;
    }

    public static function fromArray(array $data): self
    {
        $data = self::validatedUsersShape($data);
        $collection = new self();

        foreach ($data as $model) {
            $collection->add(User::fromArray($model));
        }

        return $collection;
    }

    /**
     * Validate the shape of the users array. Throws an exception if the data is invalid.
     *
     * @param list<UserShape> $data
     *
     * @throws InvalidArgumentException
     *
     * @return UsersShape
     */
    public static function validatedUsersShape(array $data): array
    {
        return $data;
    }
}
