<?php

declare(strict_types=1);

namespace OpenFGA;

use OpenFGA\API\Endpoints\{AuthorizationModelsEndpoint, StoresEndpoint};
use OpenFGA\SDK\Configuration\ClientConfigurationInterface;
use OpenFGA\SDK\Configuration\Credentials\ClientCredentialConfiguration;
use OpenFGA\SDK\Credentials\{ClientCredentialManager, CredentialManagerInterface, NullCredentialManager};

final class Client implements ClientInterface
{
    use StoresEndpoint, AuthorizationModelsEndpoint;

    public const string VERSION = '0.1.0';

    public function __construct(
        private ClientConfigurationInterface $configuration,
        private ?CredentialManagerInterface $credentialManager = null,
    ) {
    }

    public function getConfiguration(): ClientConfigurationInterface
    {
        return $this->configuration;
    }

    public function getCredentialManager(): CredentialManagerInterface
    {
        if ($this->credentialManager === null) {
            $credential = $this->getConfiguration()->getCredentialConfiguration();

            if ($credential instanceof ClientCredentialConfiguration) {
                $this->credentialManager = new ClientCredentialManager($this);
            } else {
                $this->credentialManager = new NullCredentialManager($this);
            }
        }

        return $this->credentialManager;
    }
}
