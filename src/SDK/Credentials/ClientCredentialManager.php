<?php

declare(strict_types=1);

namespace OpenFGA\SDK\Credentials;

use Exception;
use OpenFGA\ClientInterface;
use OpenFGA\SDK\Configuration\Credentials\ClientCredentialConfigurationInterface;
use OpenFGA\SDK\Utilities\Network;
use OpenFGA\SDK\Utilities\RequestBodyFormat;

final class ClientCredentialManager implements CredentialManagerInterface
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

        $configuration = $this->client->getConfiguration()->getCredentialConfiguration();

        if (! $configuration instanceof ClientCredentialConfigurationInterface) {
            throw new Exception('Invalid credential configuration');
        }

        $network = new Network(
            client: $this->client
        );

        $body = $network->createRequestBody([
            'grant_type' => 'client_credentials',
            'client_id' => $configuration->getClientId(),
            'client_secret' => $configuration->getClientSecret(),
            'audience' => $configuration->getApiAudience(),
        ], RequestBodyFormat::POST);

        $headers = [
            'User-Agent' => $network->getUserAgent(),
            'Accept' => 'application/json',
            'Content-Type' => 'application/x-www-form-urlencoded',
        ];

        $request = $network->createRequest(
            'POST',
            "https://{$configuration->getApiIssuer()}/oauth/token",
            $headers,
            $body
        );

        // TODO: Support auto-retry w/ jitter for throttled requests

        try {
            $response = $network->getHttpClient()->sendRequest($request);
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
