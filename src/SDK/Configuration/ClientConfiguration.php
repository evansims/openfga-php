<?php

declare(strict_types=1);

namespace OpenFGA\SDK\Configuration;

use InvalidArgumentException;
use OpenFGA\SDK\Configuration\Credentials\CredentialConfigurationInterface;
use OpenFGA\SDK\Utilities\Assert;

final class ClientConfiguration extends Configuration implements ClientConfigurationInterface
{
    public function __construct(
        private array $configuration = [],
        public ?string $apiUrl = null,
        public ?string $storeId = null,
        public ?string $authorizationModelId = null,
        public ?CredentialConfigurationInterface $credentialConfiguration = null,
    ) {
    }

    public function validate(): void
    {
        if (null === $this->apiUrl || ! Assert::Url($this->apiUrl)) {
            throw new InvalidArgumentException('Invalid URL');
        }
    }
}
