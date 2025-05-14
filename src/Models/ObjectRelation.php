<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class ObjectRelation implements ObjectRelationInterface
{
    public function __construct(
        private ?string $object = null,
        private ?string $relation = null,
    ) {
    }

    public function getObject(): ?string
    {
        return $this->object;
    }

    public function getRelation(): ?string
    {
        return $this->relation;
    }

    public function jsonSerialize(): array
    {
        $response = [];

        if (null !== $this->getObject()) {
            $response['object'] = $this->getObject();
        }

        if (null !== $this->getRelation()) {
            $response['relation'] = $this->getRelation();
        }

        return $response;
    }

    public static function fromArray(array $data): self
    {
        $data = self::validatedObjectRelationShape($data);

        return new self(
            object: $data['object'] ?? null,
            relation: $data['relation'] ?? null,
        );
    }

    /**
     * Validates the shape of the array to be used as object relation data. Throws an exception if the data is invalid.
     *
     * @param array{object?: string, relation?: string} $data
     *
     * @throws InvalidArgumentException
     *
     * @return ObjectRelationShape
     */
    public static function validatedObjectRelationShape(array $data): array
    {
        return $data;
    }
}
