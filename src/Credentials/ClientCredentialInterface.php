<?php

declare(strict_types=1);

namespace OpenFGA\Credentials;

interface ClientCredentialInterface extends CredentialInterface
{
    public function getApiIssuer(): ?string;

    public function getApiAudience(): ?string;

    public function getClientId(): ?string;

    public function getClientSecret(): ?string;
}
