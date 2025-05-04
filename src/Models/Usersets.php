<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class Usersets extends ModelCollection implements UsersetsInterface
{
    public function add(UsersetInterface $userset): void {
        $this->models[] = $userset;
    }

    public function current(): UsersetInterface {
        return $this->models[$this->key()];
    }

    public function offsetGet(mixed $offset): ?UsersetInterface {
        return $this->models[$offset] ?? null;
    }

    public static function fromArray(array $data): self {
        $collection = new self();

        foreach ($data as $model) {
            $collection->add(Userset::fromArray($model));
        }

        return $collection;
    }
}
