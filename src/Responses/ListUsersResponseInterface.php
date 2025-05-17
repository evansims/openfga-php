<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\UsersInterface;
use OpenFGA\Schema\SchemaInterface;

/**
 * @template T of array{users: array<mixed>}
 *
 * @extends ResponseInterface<T>
 */
interface ListUsersResponseInterface extends ResponseInterface
{
    public function getUsers(): UsersInterface;

    public static function schema(): SchemaInterface;
}
