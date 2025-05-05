<?php

declare(strict_types=1);

namespace OpenFGA\Authentication;

use OpenFGA\ClientInterface;

final class NullCredentialAuthentication implements AuthenticationInterface
{
    public function __construct(
        public ClientInterface $client,
        public ?AccessToken $accessToken = null,
    ) {
    }

    public function getAuthorizationHeader(): ?string
    {
        return null;
    }
}
