<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\UsersInterface;
use OpenFGA\Schema\SchemaInterface;

interface ListUsersResponseInterface extends ResponseInterface
{
    public function getUsers(): UsersInterface;

    public static function Schema(): SchemaInterface;
}
