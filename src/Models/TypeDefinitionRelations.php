<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class TypeDefinitionRelations implements TypeDefinitionRelationsInterface
{
    use CollectionTrait;

    public function add(UsersetInterface $userset): void
    {
        $this->models[] = $userset;
    }

    public function current(): UsersetInterface
    {
        return $this->models[$this->key()];
    }

    public function jsonSerialize(): array
    {
        $response = [];

        foreach ($this->models as $key => $model) {
            $response[$key] = $model->jsonSerialize();
        }

        return $response;
    }

    public function offsetGet(mixed $offset): ?UsersetInterface
    {
        return $this->models[$offset] ?? null;
    }

    public static function fromArray(array $data): self
    {
        $data = self::validatedRelationReferencesShape($data);
        $collection = new self();

        foreach ($data as $key => $model) {
            $collection->offsetSet($key, Userset::fromArray($model));
        }

        return $collection;
    }

    /**
     * Validate the shape of the relation references array.
     *
     * @param array<string, UsersetShape> $data
     *
     * @return TypeDefinitionRelationsShape
     */
    public static function validatedRelationReferencesShape(array $data): array
    {
        return $data;
    }
}
