<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface LeafInterface extends ModelInterface
{
    public function getComputed(): ?ComputedInterface;

    public function getTupleToUserset(): ?UsersetTreeTupleToUsersetInterface;

    public function getUsers(): ?UsersetInterface;
}
