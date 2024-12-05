<?php

declare(strict_types=1);

namespace OpenFGA\SDK\Configuration;

use OpenFGA\SDK\Configuration\Credentials\CredentialConfigurationInterface;

interface ClientConfigurationInterface extends ConfigurationInterface
{
    public function getApiUrl(): ?string;

    public function getStoreId(): ?string;

    public function getAuthorizationModelId(): ?string;

    public function getCredentialConfiguration(): ?CredentialConfigurationInterface;
}
