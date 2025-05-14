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
        $collection = new self();

        foreach ($data as $model) {
            $collection->add(User::fromArray($model));
        }

        return $collection;
    }
}
