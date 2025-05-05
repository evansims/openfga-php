<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class RelationReferences extends ModelCollection implements RelationReferencesInterface
{
    public function add(RelationReferenceInterface $relationReference): void
    {
        $this->models[] = $relationReference;
    }

    public function current(): RelationReferenceInterface
    {
        return $this->models[$this->key()];
    }

    public function offsetGet(mixed $offset): ?RelationReferenceInterface
    {
        return $this->models[$offset] ?? null;
    }

    public static function fromArray(array $data): self
    {
        $collection = new self();

        foreach ($data as $model) {
            $collection->add(RelationReference::fromArray($model));
        }

        return $collection;
    }
}
