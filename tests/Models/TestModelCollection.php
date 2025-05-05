<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Models;

use OpenFGA\Models\ModelCollection;
use OpenFGA\Models\ModelInterface;

class TestModelCollection extends ModelCollection
{
    public function add(ModelInterface $model): void
    {
        $this->models[] = $model;
    }

    public function current(): ModelInterface
    {
        return $this->models[$this->key()];
    }

    public function offsetGet(mixed $offset): ?ModelInterface
    {
        return $this->models[$offset] ?? null;
    }

    public static function fromArray(array $data): self
    {
        $collection = new self();

        foreach ($data as $item) {
            $collection->add(TestModel::fromArray($item));
        }

        return $collection;
    }
}
