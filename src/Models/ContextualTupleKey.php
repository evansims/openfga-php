<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class ContextualTupleKey extends Model implements ContextualTupleKeyInterface
{
    public function __construct(
        private string $user,
        private string $relation,
        private string $object,
        private string $condition,
    ) {
    }

    public function getCondition(): string
    {
        return $this->condition;
    }

    public function getObject(): string
    {
        return $this->object;
    }

    public function getRelation(): string
    {
        return $this->relation;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function toArray(): array
    {
        return [
            'user' => $this->user,
            'relation' => $this->relation,
            'object' => $this->object,
            'condition' => $this->condition,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            user: $data['user'],
            relation: $data['relation'],
            object: $data['object'],
            condition: $data['condition'],
        );
    }
}
