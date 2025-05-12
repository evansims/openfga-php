<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface UserInterface extends ModelInterface
{
    public function getDifference(): ?DifferenceV1;

    public function getUser(): ?object;

    public function getUserset(): ?UsersetUser;

    public function getWildcard(): ?TypedWildcard;
}
