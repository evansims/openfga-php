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
        $data = self::validatedTypeDefinitionsShape($data);
        $collection = new self();

        foreach ($data as $model) {
            $collection->add(TypeDefinition::fromArray($model));
        }

        return $collection;
    }

    /**
     * Validates the shape of the array to be used as type definitions data. Throws an exception if the data is invalid.
     *
     * @param list<TypeDefinitionShape> $data
     *
     * @throws InvalidArgumentException
     *
     * @return TypeDefinitionsShape
     */
    public static function validatedTypeDefinitionsShape(array $data): array
    {
        return $data;
    }
}
