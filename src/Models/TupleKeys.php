<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class TupleKeys extends ModelCollection implements TupleKeysInterface
{
    public function add(TupleKeyInterface $tupleKey): void {
        $this->models[] = $tupleKey;
    }

    public function current(): TupleKeyInterface {
        return $this->models[$this->key()];
    }

    public function offsetGet(mixed $offset): ?TupleKeyInterface {
        return $this->models[$offset] ?? null;
    }

    public static function fromArray(array $data): self {
        $collection = new self();

        foreach ($data as $model) {
            $collection->add(TupleKey::fromArray($model));
        }

        return $collection;
    }
}
