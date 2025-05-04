<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class Stores extends ModelCollection implements StoresInterface
{
    public function add(StoreInterface $store): void {
        $this->models[] = $store;
    }

    public function current(): StoreInterface {
        return $this->models[$this->key()];
    }

    public function offsetGet(mixed $offset): ?StoreInterface {
        return $this->models[$offset] ?? null;
    }

    public static function fromArray(array $data): self {
        $collection = new self();

        foreach ($data as $model) {
            $collection->add(Store::fromArray($model));
        }

        return $collection;
    }
}
