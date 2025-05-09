<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface UsersetTreeTupleToUsersetInterface extends ModelInterface
{
    public function getBase(): Node;

    public function getSubtract(): Node;
}
