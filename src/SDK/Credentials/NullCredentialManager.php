<?php

declare(strict_types=1);

namespace OpenFGA\SDK\Credentials;

use OpenFGA\ClientInterface;

final class NullCredentialManager implements CredentialManagerInterface
{
    public function __construct(
        public ClientInterface $client,
        public ?AccessToken $accessToken = null,
    ) {
    }

    public function getAuthorizationHeader(): ?string {
        return null;
    }
}
