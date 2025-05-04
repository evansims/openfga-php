<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class TupleKey extends Model implements TupleKeyInterface
{
    public function __construct(
        public string $user,
        public string $relation,
        public string $object,
        public ?Condition $condition = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            'user' => $this->user,
            'relation' => $this->relation,
            'object' => $this->object,
            'condition' => $this->condition ? $this->condition->toArray() : null,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            user: $data['user'],
            relation: $data['relation'],
            object: $data['object'],
            condition: isset($data['condition']) ? Condition::fromArray($data['condition']) : null,
        );
    }
}
