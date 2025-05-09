<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface LeafInterface extends ModelInterface
{
    public function getComputed(): ?Computed;

    public function getTupleToUserset(): ?UsersetTreeTupleToUserset;

    public function getUsers(): ?Users;
}
