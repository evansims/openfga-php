<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use InvalidArgumentException;

final class UsersetUser implements UsersetUserInterface
{
    public function __construct(
        private string $type,
        private string $id,
        private string $relation,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getRelation(): string
    {
        return $this->relation;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function jsonSerialize(): array
    {
        return [
            'type' => $this->getType(),
            'id' => $this->getId(),
            'relation' => $this->getRelation(),
        ];
    }

    public static function fromArray(array $data): self
    {
        $data = self::validatedUsersetUserShape($data);

        return new self(
            type: $data['type'],
            id: $data['id'],
            relation: $data['relation'],
        );
    }

    /**
     * @param array{type: string, id: string, relation: string} $data
     *
     * @return UsersetUserShape
     */
    public static function validatedUsersetUserShape(array $data): array
    {
        if (! isset($data['type'])) {
            throw new InvalidArgumentException('UsersetUser must have a type');
        }

        if (! isset($data['id'])) {
            throw new InvalidArgumentException('UsersetUser must have an id');
        }

        if (! isset($data['relation'])) {
            throw new InvalidArgumentException('UsersetUser must have a relation');
        }

        return $data;
    }
}
