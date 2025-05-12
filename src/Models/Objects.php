<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class Objects extends ModelCollection implements ObjectsInterface
{
    public function add(ObjectInterface $object): void
    {
        $this->models[] = $object;
    }

    public function current(): ObjectInterface
    {
        return $this->models[$this->key()];
    }

    public function offsetGet(mixed $offset): ?ObjectInterface
    {
        return $this->models[$offset] ?? null;
    }

    public static function fromArray(array $data): self
    {
        $collection = new self();

        foreach ($data as $model) {
            $collection->add(Object::fromArray($model));
        }

        return $collection;
    }
}
