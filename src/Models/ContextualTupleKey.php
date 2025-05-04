<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class ContextualTupleKey extends Model implements ContextualTupleKeyInterface
{
    public function __construct(
        public string $user,
        public string $relation,
        public string $object,
        public string $condition,
    ) {
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
