<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\UsersInterface;

interface ListUsersResponseInterface extends ResponseInterface
{
    public function getUsers(): UsersInterface;

    public static function fromArray(array $data): static;
}
