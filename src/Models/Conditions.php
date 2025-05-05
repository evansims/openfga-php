<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class Conditions extends ModelCollection implements ConditionsInterface
{
    public function add(ConditionInterface $condition): void
    {
        $this->models[] = $condition;
    }

    public function current(): ConditionInterface
    {
        return $this->models[$this->key()];
    }

    public function offsetGet(mixed $offset): ?ConditionInterface
    {
        return $this->models[$offset] ?? null;
    }

    public static function fromArray(array $data): self
    {
        $collection = new self();

        foreach ($data as $model) {
            $collection->add(Condition::fromArray($model));
        }

        return $collection;
    }
}
