<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class UsersetUser extends Model implements UsersetUserInterface
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

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'id' => $this->id,
            'relation' => $this->relation,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            type: $data['type'],
            id: $data['id'],
            relation: $data['relation'],
        );
    }
}
