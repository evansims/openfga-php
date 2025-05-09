<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface UsersetTreeDifferenceInterface extends ModelInterface
{
    public function getBase(): NodeInterface;

    public function getSubtract(): NodeInterface;
}
