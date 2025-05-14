<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class Stores implements StoresInterface
{
    use CollectionTrait;

    public function add(StoreInterface $store): void
    {
        $this->models[] = $store;
    }

    public function current(): StoreInterface
    {
        return $this->models[$this->key()];
    }

    public function offsetGet(mixed $offset): ?StoreInterface
    {
        return $this->models[$offset] ?? null;
    }

    public static function fromArray(array $data): self
    {
        $data = self::validatedStoresShape($data);
        $collection = new self();

        foreach ($data as $model) {
            $collection->add(Store::fromArray($model));
        }

        return $collection;
    }

    /**
     * Validate the shape of the stores array.
     *
     * @param list<StoreShape> $data
     *
     * @return StoresShape
     */
    public static function validatedStoresShape(array $data): array
    {
        return $data;
    }
}
