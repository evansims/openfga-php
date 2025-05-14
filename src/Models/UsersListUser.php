<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class UsersListUser implements UsersListUserInterface
{
    public function __construct(
        private string $user,
    ) {
    }

    public function __toString(): string
    {
        return $this->getUser();
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function jsonSerialize(): string
    {
        return $this->getUser();
    }
}
