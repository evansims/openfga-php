<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class TypeDefinitions implements TypeDefinitionsInterface
{
    use CollectionTrait;

    public function add(TypeDefinitionInterface $typeDefinition): void
    {
        $this->models[] = $typeDefinition;
    }

    public function current(): TypeDefinitionInterface
    {
        return $this->models[$this->key()];
    }

    public function offsetGet(mixed $offset): ?TypeDefinitionInterface
    {
        return $this->models[$offset] ?? null;
    }

    public static function fromArray(array $data): self
    {
        $collection = new self();

        foreach ($data as $model) {
            $collection->add(TypeDefinition::fromArray($model));
        }

        return $collection;
    }
}
