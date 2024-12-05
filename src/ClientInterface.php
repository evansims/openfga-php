<?php

declare(strict_types=1);

namespace OpenFGA;

use OpenFGA\SDK\Configuration\ClientConfigurationInterface;
use OpenFGA\SDK\Credentials\CredentialManagerInterface;

interface ClientInterface
{
    public function getConfiguration(): ClientConfigurationInterface;

    public function getCredentialManager(): CredentialManagerInterface;
}
