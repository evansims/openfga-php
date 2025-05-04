<?php

declare(strict_types=1);

namespace OpenFGA\Authentication;

use Exception;
use OpenFGA\ClientInterface;
use OpenFGA\Credentials\ClientCredentialInterface;
use OpenFGA\Requests\RequestBodyFormat;

final class ClientCredentialAuthentication implements AuthenticationInterface
{
    public function __construct(
        private ClientInterface $client,
        private ?AccessToken $accessToken = null,
    ) {
    }

    public function getAuthorizationHeader(): ?string {
        return 'Bearer ' . (string)$this->getAccessToken();
    }

    public function getAccessToken(): AccessToken {
        if ($this->accessToken && ! $this->accessToken->isExpired()) {
            return $this->accessToken;
        }

        $configuration = $this->client->getConfiguration()->getCredential();

        if (! $configuration instanceof ClientCredentialInterface) {
            throw new Exception('Invalid credential configuration');
        }

        $factory = $this->client->getRequestFactory();

        $body = $factory->body([
            'grant_type' => 'client_credentials',
            'client_id' => $configuration->getClientId(),
            'client_secret' => $configuration->getClientSecret(),
            'audience' => $configuration->getApiAudience(),
        ], RequestBodyFormat::POST);

        $request = $factory->post(
            url: "https://{$configuration->getApiIssuer()}/oauth/token",
            body: $body,
            headers: [
                'Accept' => 'application/json',
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
        );

        // TODO: Support auto-retry w/ jitter for throttled requests

        try {
            $response = $factory->send($request->getRequest());
        } catch (Exception $e) {
            throw new Exception('API request issuance failed');
        }

        if (200 !== $response->getStatusCode()) {
            throw new Exception('API request failed');
        }

        try {
            $response = json_decode($response->getBody()->getContents(), true);
        } catch (Exception $e) {
            throw new Exception('API response parsing failed');
        }

        if (!isset($response['access_token']) || !isset($response['expires_in'])) {
            throw new Exception('API response missing expected fields');
        }

        // TODO: Cache this response

        return $this->accessToken = new AccessToken(
            token: $response['access_token'],
            expires: time() + $response['expires_in'],
            scope: $response['scope'] ?? null,
        );
    }
}
