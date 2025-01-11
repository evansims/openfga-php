<?php

declare(strict_types=1);

namespace OpenFGA\SDK\Configuration\Credentials;

interface SharedKeyCredentialConfigurationInterface extends CredentialConfigurationInterface
{
    public function getSharedKey(): ?string;

    public function setSharedKey(?string $sharedKey): self;
}
