<?php

declare(strict_types=1);

namespace OpenFGA\Credentials;

final class TokenCredential extends Credential implements TokenCredentialInterface
{
    public function __construct(
        private array $configuration = [],
        public ?string $apiToken = null,
    ) {
    }
}
