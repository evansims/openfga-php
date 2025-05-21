<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Models\Collections\UsersListInterface;
use Override;

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
    #[Override]
    public function jsonSerialize(): array;
}
