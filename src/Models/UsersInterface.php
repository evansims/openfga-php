<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface UsersInterface extends ModelInterface
{
    public function getUsers(): array;
}
