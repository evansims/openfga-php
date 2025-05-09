<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class TupleKey extends Model implements TupleKeyInterface
{
    public function __construct(
        public ?string $user = null,
        public ?string $relation = null,
        public ?string $object = null,
        public ?Condition $condition = null,
    ) {
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
