<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface TypedWildcardInterface extends ModelInterface
{
    public function getType(): string;
}
