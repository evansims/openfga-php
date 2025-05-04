<?php

declare(strict_types=1);

namespace OpenFGA\Credentials;

use InvalidArgumentException;

final class TokenCredential extends Credential implements TokenCredentialInterface
{
    public function __construct(
        private array $configuration = [],
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
