<?php

declare(strict_types=1);

namespace OpenFGA\Credentials;

final class ClientCredential extends Credential implements ClientCredentialInterface
{
    public function __construct(
        private array $configuration = [],
        private ?string $apiIssuer = null,
        private ?string $apiAudience = null,
        private ?string $clientId = null,
        private ?string $clientSecret = null,
    ) {
    }

    public function getApiAudience(): ?string
    {
        return $this->apiAudience;
    }

    public function getApiIssuer(): ?string
    {
        return $this->apiIssuer;
    }

    public function getClientId(): ?string
    {
        return $this->clientId;
    }

    public function getClientSecret(): ?string
    {
        return $this->clientSecret;
    }
}
