<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use InvalidArgumentException;

final class RelationReference implements RelationReferenceInterface
{
    /**
     * Constructs a new RelationReference object.
     *
     * @param string      $type
     * @param null|string $relation
     * @param null|object $wildcard
     * @param null|string $condition
     */
    public function __construct(
        private string $type,
        private ?string $relation = null,
        private ?object $wildcard = null,
        private ?string $condition = null,
    ) {
    }

    public function getCondition(): ?string
    {
        return $this->condition;
    }

    public function getRelation(): ?string
    {
        return $this->relation;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getWildcard(): ?object
    {
        return $this->wildcard;
    }

    public function jsonSerialize(): array
    {
        $response = [
            'type' => $this->type,
        ];

        if (null !== $this->getRelation()) {
            $response['relation'] = $this->getRelation();
        }

        if (null !== $this->getWildcard()) {
            $response['wildcard'] = $this->getWildcard();
        }

        if (null !== $this->getCondition()) {
            $response['condition'] = $this->getCondition();
        }

        return $response;
    }

    public static function fromArray(array $data): self
    {
        $data = self::validatedRelationReferenceShape($data);

        return new self(
            type: $data['type'],
            relation: $data['relation'] ?? null,
            wildcard: $data['wildcard'] ?? null,
            condition: $data['condition'] ?? null,
        );
    }

    /**
     * Validates the shape of the relation reference data.
     *
     * @param array{type: string, relation?: string, wildcard?: object, condition?: string} $data
     *
     * @throws InvalidArgumentException
     *
     * @return RelationReferenceShape
     */
    public static function validatedRelationReferenceShape(array $data): array
    {
        if (! isset($data['type'])) {
            throw new InvalidArgumentException('Missing type');
        }

        return $data;
    }
}
