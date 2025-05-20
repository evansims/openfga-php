<?php

declare(strict_types=1);

namespace OpenFGA\Authentication;

use OpenFGA\Network\RequestContext;
use OpenFGA\Network\RequestMethod;
use Psr\Http\Message\StreamFactoryInterface;

final class ClientCredentialAuthentication implements AuthenticationInterface
{
    public function __construct(
        private readonly string $clientId,
        private readonly string $clientSecret,
        private readonly string $audience,
        private readonly string $issuer,
    ) {
    }

    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
    {
        return new RequestContext(
            method: RequestMethod::POST,
            url: '/oauth/token',
            body: $streamFactory->createStream(json_encode([
                'grant_type' => 'client_credentials',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'audience' => $this->audience,
            ], JSON_THROW_ON_ERROR)),
            headers: [
                'Accept' => 'application/json',
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
        );
    }
}
