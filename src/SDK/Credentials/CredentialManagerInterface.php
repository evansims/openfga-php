<?php

declare(strict_types=1);

namespace OpenFGA\SDK\Credentials;

interface CredentialManagerInterface
{
    public function getAuthorizationHeader(): ?string;
}
