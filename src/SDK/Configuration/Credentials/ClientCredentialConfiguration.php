<?php

declare(strict_types=1);

namespace OpenFGA\SDK\Configuration\Credentials;

use InvalidArgumentException;
use OpenFGA\SDK\Utilities\Assert;

final class ClientCredentialConfiguration extends CredentialConfiguration
{
    public function __construct(
        private array $configuration,
        public ?string $apiIssuer = null,
        public ?string $apiAudience = null,
        public ?string $clientId = null,
        public ?string $clientSecret = null,
    ) {
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
