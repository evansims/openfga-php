<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class TupleKey extends Model implements TupleKeyInterface
{
    public function __construct(
        private ?string $user = null,
        private ?string $relation = null,
        private ?string $object = null,
        private ?ConditionInterface $condition = null,
    ) {
    }

    public function getCondition(): ?ConditionInterface
    {
        return $this->condition;
    }

    public function getObject(): ?string
    {
        return $this->object;
    }

    public function getRelation(): ?string
    {
        return $this->relation;
    }

    public function getUser(): ?string
    {
        return $this->user;
    }

    public function toArray(): array
    {
        return [
            'user' => $this->user ?? null,
            'relation' => $this->relation ?? null,
            'object' => $this->object ?? null,
            'condition' => $this->condition ? $this->condition->toArray() : null,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            user: $data['user'] ?? null,
            relation: $data['relation'] ?? null,
            object: $data['object'] ?? null,
            condition: isset($data['condition']) ? Condition::fromArray($data['condition']) : null,
        );
    }
}
