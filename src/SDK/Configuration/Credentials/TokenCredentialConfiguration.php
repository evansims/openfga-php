<?php

declare(strict_types=1);

namespace OpenFGA\SDK\Configuration\Credentials;

use InvalidArgumentException;

final class TokenCredentialConfiguration extends CredentialConfiguration
{
    public function __construct(
        private array $configuration,
        public ?string $apiToken = null,
    ) {
    }

    public function validate(): void
    {
        if (null === $this->apiToken) {
            throw new InvalidArgumentException('Invalid Token');
        }
    }
}
