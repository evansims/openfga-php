<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @psalm-type LeafShape = array{users?: UsersListShape, computed?: ComputedShape, tupleToUserset?: UsersetTreeTupleToUsersetShape}
 */
interface LeafInterface extends ModelInterface
{
    public function getComputed(): ?ComputedInterface;

    public function getTupleToUserset(): ?UsersetTreeTupleToUsersetInterface;

    public function getUsers(): ?UsersListInterface;

    /**
     * @return LeafShape
     */
    public function jsonSerialize(): array;
}
