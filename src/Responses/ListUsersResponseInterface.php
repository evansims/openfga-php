<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\Collections\UsersInterface;
use OpenFGA\Models\UserInterface;
use OpenFGA\Schema\SchemaInterface;

interface ListUsersResponseInterface extends ResponseInterface
{
    /**
     * @return UsersInterface<UserInterface>
     */
    public function getUsers(): UsersInterface;

    public static function schema(): SchemaInterface;
}
