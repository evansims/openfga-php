<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class Leaf extends Model implements LeafInterface
{
    public function __construct(
        private ?Users $users,
        private ?Computed $computed,
        private ?UsersetTreeTupleToUserset $tupleToUserset,
    ) {
    }

    public function getComputed(): ?Computed
    {
        return $this->computed;
    }

    public function getTupleToUserset(): ?UsersetTreeTupleToUserset
    {
        return $this->tupleToUserset;
    }

    public function getUsers(): ?Users
    {
        return $this->users;
    }

    public function toArray(): array
    {
        return [
            'users' => $this->users ? $this->users->toArray() : null,
            'computed' => $this->computed ? $this->computed->toArray() : null,
            'tupleToUserset' => $this->tupleToUserset ? $this->tupleToUserset->toArray() : null,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            users: isset($data['users']) ? Users::fromArray($data['users']) : null,
            computed: isset($data['computed']) ? Computed::fromArray($data['computed']) : null,
            tupleToUserset: isset($data['tupleToUserset']) ? UsersetTreeTupleToUserset::fromArray($data['tupleToUserset']) : null,
        );
    }
}
