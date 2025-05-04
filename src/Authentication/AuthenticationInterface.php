<?php

declare(strict_types=1);

namespace OpenFGA\Authentication;

interface AuthenticationInterface
{
    public function getAuthorizationHeader(): ?string;
}
