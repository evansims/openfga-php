<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface UsersetTreeInterface extends ModelInterface
{
    public function getNode(): Node;
}
