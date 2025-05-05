<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class ContextualTupleKeys extends ModelCollection implements ContextualTupleKeysInterface
{
    public function add(ContextualTupleKeyInterface $tupleKey): void
    {
        $this->models[] = $tupleKey;
    }

    public function current(): ContextualTupleKeyInterface
    {
        return $this->models[$this->key()];
    }

    public function offsetGet(mixed $offset): ?ContextualTupleKeyInterface
    {
        return $this->models[$offset] ?? null;
    }

    public static function fromArray(array $data): self
    {
        $collection = new self();

        foreach ($data as $model) {
            $collection->add(ContextualTupleKey::fromArray($model));
        }

        return $collection;
    }
}
