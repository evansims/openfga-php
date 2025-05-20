<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Models\Collections\UsersListInterface;

interface LeafInterface extends ModelInterface
{
    public function getComputed(): ?ComputedInterface;

    public function getTupleToUserset(): ?UsersetTreeTupleToUsersetInterface;

    /**
     * @return null|UsersListInterface<UsersListUserInterface>
     */
    public function getUsers(): ?UsersListInterface;

    /**
     * @return array{users?: array<int, string>, computed?: array{userset: string}, tupleToUserset?: mixed}
     */
    public function jsonSerialize(): array;
}
