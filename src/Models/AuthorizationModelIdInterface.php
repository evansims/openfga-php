<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface AuthorizationModelIdInterface extends ModelInterface
{
    public function __toString(): string;

    public static function fromAuthorizationModel(AuthorizationModel $authorizationModel): self;
}
