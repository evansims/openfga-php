<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class AssertionTupleKey extends Model implements AssertionTupleKeyInterface
{
    public function __construct(
        public string $user,
        public string $relation,
        public string $object,
    ) {
    }

    public function toArray(): array
    {
        return [
            'user' => $this->user,
            'relation' => $this->relation,
            'object' => $this->object,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            user: $data['user'],
            relation: $data['relation'],
            object: $data['object'],
        );
    }
}
