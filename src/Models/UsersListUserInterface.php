<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use Override;

interface UsersListUserInterface extends ModelInterface
{
    public function __toString(): string;

    public function getUser(): string;

    #[Override]
    public function jsonSerialize(): string;
}
