<?php

declare(strict_types=1);

namespace OpenFGA\SDK\Configuration\Credentials;

interface ClientCredentialConfigurationInterface extends CredentialConfigurationInterface
{
    public function getApiIssuer(): ?string;

    public function getApiAudience(): ?string;

    public function getClientId(): ?string;

    public function getClientSecret(): ?string;
}
