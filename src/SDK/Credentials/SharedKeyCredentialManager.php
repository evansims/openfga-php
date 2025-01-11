<?php

declare(strict_types=1);

namespace OpenFGA\SDK\Credentials;

use OpenFGA\ClientInterface;
use OpenFGA\SDK\Configuration\Credentials\SharedKeyCredentialConfigurationInterface;

final class SharedKeyCredentialManager implements CredentialManagerInterface
{
    public function __construct(
        private ClientInterface $client,
        private ?string $sharedKey = null,
    ) {
    }

    public function getAuthorizationHeader(): ?string {
        return $this->getSharedKey() ? 'Bearer ' . $this->getSharedKey() : null;
    }

    public function getSharedKey(): ?string {
        if (null === $this->sharedKey) {
            $configuration = $this->client->getConfiguration()->getCredentialConfiguration();

            if ($configuration instanceof SharedKeyCredentialConfigurationInterface) {
                $this->sharedKey = $configuration->getSharedKey();
            }
        }

        return $this->sharedKey;
    }
}
