<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use InvalidArgumentException;

use function is_string;

final class UsersList implements UsersListInterface
{
    use CollectionTrait;

    public function add(UsersListUserInterface $user): void
    {
        $this->models[] = $user;
    }

    public function current(): UsersListUserInterface
    {
        return $this->models[$this->key()];
    }

    public function jsonSerialize(): array
    {
        $response = [];

        foreach ($this->models as $model) {
            $response[] = (string) $model;
        }

        return $response;
    }

    public function offsetGet(mixed $offset): ?UsersListUserInterface
    {
        return $this->models[$offset] ?? null;
    }

    public static function fromArray(array $data): self
    {
        $data = self::validatedUsersListShape($data);
        $collection = new self();

        foreach ($data as $model) {
            $collection->add(new UsersListUser($model));
        }

        return $collection;
    }

    /**
     * @param list<string> $data
     *
     * @return UsersListShape
     */
    public static function validatedUsersListShape(array $data): array
    {
        foreach ($data as $model) {
            if (! is_string($model)) {
                throw new InvalidArgumentException('UsersList must be a list of strings');
            }
        }

        return $data;
    }
}
