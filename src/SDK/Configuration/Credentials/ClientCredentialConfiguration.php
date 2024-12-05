<?php

declare(strict_types=1);

namespace OpenFGA\SDK\Configuration\Credentials;

use InvalidArgumentException;
use OpenFGA\SDK\Utilities\Assert;

final class ClientCredentialConfiguration extends CredentialConfiguration implements ClientCredentialConfigurationInterface
{
    public function __construct(
        private array $configuration = [],
        private ?string $apiIssuer = null,
        private ?string $apiAudience = null,
        private ?string $clientId = null,
        private ?string $clientSecret = null,
    ) {
    }

    public function getApiIssuer(): ?string
    {
        return $this->apiIssuer;
    }

    public function getApiAudience(): ?string
    {
        return $this->apiAudience;
    }

    public function getClientId(): ?string
    {
        return $this->clientId;
    }

    public function getClientSecret(): ?string
    {
        return $this->clientSecret;
    }

    public function validate(): void
    {
        if (null === $this->apiIssuer || ! Assert::Url($this->apiIssuer)) {
            throw new InvalidArgumentException('Invalid URL');
        }

        if (null === $this->apiAudience || ! Assert::Url($this->apiAudience)) {
            throw new InvalidArgumentException('Invalid URL');
        }
    }
}
