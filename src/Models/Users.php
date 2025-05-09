<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class Users extends Model implements UsersInterface
{
    public function __construct(
        private array $users,
    ) {
    }

    public function getUsers(): array
    {
        return $this->users;
    }

    public function toArray(): array
    {
        return [
            'users' => $this->users,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            users: $data['users'],
        );
    }
}
