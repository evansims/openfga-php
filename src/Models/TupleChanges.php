<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class TupleChanges implements TupleChangesInterface
{
    use CollectionTrait;

    public function add(TupleChangeInterface $tupleChange): void
    {
        $this->models[] = $tupleChange;
    }

    public function current(): TupleChangeInterface
    {
        return $this->models[$this->key()];
    }

    public function offsetGet(mixed $offset): ?TupleChangeInterface
    {
        return $this->models[$offset] ?? null;
    }

    public static function fromArray(array $data): self
    {
        $collection = new self();

        foreach ($data as $model) {
            $collection->add(TupleChange::fromArray($model));
        }

        return $collection;
    }
}
