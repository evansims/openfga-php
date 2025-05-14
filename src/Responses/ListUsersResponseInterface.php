<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\UsersInterface;

interface ListUsersResponseInterface extends ResponseInterface
{
    /**
     * @return UsersInterface
     */
    public function getUsers(): UsersInterface;

    /**
     * @param array<string, null|string> $data
     */
    public static function fromArray(array $data): static;
}
