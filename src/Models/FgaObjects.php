<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class FgaObjects implements FgaObjectsInterface
{
    use CollectionTrait;

    public function add(FgaObjectInterface $object): void
    {
        $this->models[] = $object;
    }

    public function current(): FgaObjectInterface
    {
        return $this->models[$this->key()];
    }

    public function offsetGet(mixed $offset): ?FgaObjectInterface
    {
        return $this->models[$offset] ?? null;
    }

    public static function fromArray(array $data): self
    {
        $collection = new self();

        foreach ($data as $model) {
            $collection->add(FgaObject::fromArray($model));
        }

        return $collection;
    }
}
