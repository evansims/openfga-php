<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class Tuples extends ModelCollection implements TuplesInterface
{
    public function add(TupleInterface $tuple): void
    {
        $this->models[] = $tuple;
    }

    public function current(): TupleInterface
    {
        return $this->models[$this->key()];
    }

    public function offsetGet(mixed $offset): ?TupleInterface
    {
        return $this->models[$offset] ?? null;
    }

    public static function fromArray(array $data): self
    {
        $collection = new self();

        foreach ($data as $model) {
            $collection->add(Tuple::fromArray($model));
        }

        return $collection;
    }
}
