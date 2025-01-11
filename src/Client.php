<?php

declare(strict_types=1);

namespace OpenFGA;

use OpenFGA\API\Endpoints\{StoresEndpoint, StoreEndpoint};
use OpenFGA\SDK\Configuration\ClientConfigurationInterface;
use OpenFGA\SDK\Configuration\Credentials\ClientCredentialConfiguration;
use OpenFGA\SDK\Configuration\Credentials\ClientCredentialConfigurationInterface;
use OpenFGA\SDK\Configuration\Credentials\SharedKeyCredentialConfigurationInterface;
use OpenFGA\SDK\Credentials\{ClientCredentialManager, CredentialManagerInterface, NullCredentialManager, SharedKeyCredentialManager};

final class Client implements ClientInterface
{
    public const string VERSION = '0.1.0';

    public function __construct(
        private ClientConfigurationInterface $configuration,
        private ?CredentialManagerInterface $credentialManager = null,
        private ?StoresEndpoint $storesEndpoint = null,
    ) {
    }

    public function getConfiguration(): ClientConfigurationInterface
    {
        return $this->configuration;
    }

    public function setConfiguration(ClientConfigurationInterface $configuration): self
    {
        $this->configuration = $configuration;
        return $this;
    }

    public function getCredentialManager(): CredentialManagerInterface
    {
        if ($this->credentialManager === null) {
            $credential = $this->getConfiguration()->getCredentialConfiguration();

            if ($credential instanceof ClientCredentialConfigurationInterface) {
                $this->credentialManager = new ClientCredentialManager($this);
            } elseif ($credential instanceof SharedKeyCredentialConfigurationInterface) {
                $this->credentialManager = new SharedKeyCredentialManager($this);
            } else {
                $this->credentialManager = new NullCredentialManager($this);
            }
        }

        return $this->credentialManager;
    }

    public function setCredentialManager(CredentialManagerInterface $credentialManager): self
    {
        $this->credentialManager = $credentialManager;
        return $this;
    }

    public function stores(): StoresEndpoint
    {
        return new StoresEndpoint($this);
    }

    public function store(
        ?string $storeId = null,
    ): StoreEndpoint
    {
        return new StoreEndpoint($this, $storeId);
    }
}
