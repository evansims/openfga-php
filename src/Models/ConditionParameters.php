<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class ConditionParameters implements ConditionParametersInterface
{
    use CollectionTrait;

    public function add(ConditionParameterInterface $store): void
    {
        $this->models[] = $store;
    }

    public function current(): ConditionParameterInterface
    {
        return $this->models[$this->key()];
    }

    public function offsetGet(mixed $offset): ?ConditionParameterInterface
    {
        return $this->models[$offset] ?? null;
    }

    public static function fromArray(array $data): self
    {
        $collection = new self();

        foreach ($data as $model) {
            $collection->add(ConditionParameter::fromArray($model));
        }

        return $collection;
    }
}
