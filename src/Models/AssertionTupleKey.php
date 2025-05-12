<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class AssertionTupleKey extends Model implements AssertionTupleKeyInterface
{
    public function __construct(
        private string $user,
        private string $relation,
        private string $object,
    ) {
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
