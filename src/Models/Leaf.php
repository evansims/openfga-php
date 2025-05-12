<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class Leaf extends Model implements LeafInterface
{
    public function __construct(
        private ?UsersetInterface $users,
        private ?ComputedInterface $computed,
        private ?UsersetTreeTupleToUsersetInterface $tupleToUserset,
    ) {
    }

    public function getComputed(): ?ComputedInterface
    {
        return $this->computed;
    }

    public function getTupleToUserset(): ?UsersetTreeTupleToUsersetInterface
    {
        return $this->tupleToUserset;
    }

    public function getUsers(): ?UsersetInterface
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
