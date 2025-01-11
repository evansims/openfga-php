<?php

declare(strict_types=1);

namespace OpenFGA\SDK\Configuration\Credentials;

use InvalidArgumentException;

final class SharedKeyCredentialConfiguration extends CredentialConfiguration implements SharedKeyCredentialConfigurationInterface
{
    public function __construct(
        private array $configuration = [],
        public ?string $sharedKey = null,
    ) {
    }

    public function getSharedKey(): ?string
    {
        return $this->sharedKey;
    }

    public function setSharedKey(?string $sharedKey): self
    {
        $this->sharedKey = $sharedKey;
        return $this;
    }

    public function validate(): void
    {
        if (null === $this->sharedKey) {
            throw new InvalidArgumentException('Invalid Token');
        }
    }
}
