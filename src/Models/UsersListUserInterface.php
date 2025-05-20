<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface UsersListUserInterface extends ModelInterface
{
    public function __toString(): string;

    public function getUser(): string;

    public function jsonSerialize(): string;
}
