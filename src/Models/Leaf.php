<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class Leaf implements LeafInterface
{
    public function __construct(
        private ?UsersListInterface $users,
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

    public function getUsers(): ?UsersListInterface
    {
        return $this->users;
    }

    public function jsonSerialize(): array
    {
        $response = [];

        if (null !== $this->getUsers()) {
            $response['users'] = $this->getUsers()->jsonSerialize();
        }

        if (null !== $this->getComputed()) {
            $response['computed'] = $this->getComputed()->jsonSerialize();
        }

        if (null !== $this->getTupleToUserset()) {
            $response['tupleToUserset'] = $this->getTupleToUserset()->jsonSerialize();
        }

        return $response;
    }

    public static function fromArray(array $data): self
    {
        $data = self::validatedLeafShape($data);

        return new self(
            users: isset($data['users']) ? UsersList::fromArray($data['users']) : null,
            computed: isset($data['computed']) ? Computed::fromArray($data['computed']) : null,
            tupleToUserset: isset($data['tupleToUserset']) ? UsersetTreeTupleToUserset::fromArray($data['tupleToUserset']) : null,
        );
    }

    /**
     * @param array{users?: UsersListShape, computed?: ComputedShape, tupleToUserset?: UsersetTreeTupleToUsersetShape} $data
     *
     * @return LeafShape
     */
    public static function validatedLeafShape(array $data): array
    {
        return $data;
    }
}
