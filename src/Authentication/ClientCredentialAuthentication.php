<?php

declare(strict_types=1);

namespace OpenFGA\Authentication;

use Exception;
use OpenFGA\ClientInterface;
use OpenFGA\Credentials\ClientCredentialInterface;
use OpenFGA\Requests\RequestBodyFormat;

use function array_key_exists;
use function is_array;
use function is_int;
use function is_string;

final class ClientCredentialAuthentication implements AuthenticationInterface
{
    public function __construct(
        private ClientInterface $client,
        private ?AccessToken $accessToken = null,
    ) {
    }

    public function getAccessToken(): AccessToken
    {
        if (null !== $this->accessToken && ! $this->accessToken->isExpired()) {
            return $this->accessToken;
        }

        $config = $this->client->getConfiguration();
        $credential = $config->getCredential();

        if (null === $credential) {
            throw new Exception('Credential not set in configuration');
        }

        if (! $credential instanceof ClientCredentialInterface) {
            throw new Exception('Invalid credential configuration');
        }

        $factory = $this->client->getRequestFactory();

        $body = $factory->body([
            'grant_type' => 'client_credentials',
            'client_id' => $credential->getClientId(),
            'client_secret' => $credential->getClientSecret(),
            'audience' => $credential->getApiAudience(),
        ], RequestBodyFormat::POST);

        $request = $factory->post(
            url: "https://{$credential->getApiIssuer()}/oauth/token",
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
            $responseData = json_decode($response->getBody()->getContents(), true);

            // Ensure we have an array to work with
            if (! is_array($responseData)) {
                throw new Exception('API response parsing failed - invalid JSON structure');
            }
        } catch (Exception $e) {
            throw new Exception('API response parsing failed: ' . $e->getMessage());
        }

        // Check both presence and type of required fields
        $hasAccessToken = array_key_exists('access_token', $responseData) && is_string($responseData['access_token']);
        $hasExpiresIn = array_key_exists('expires_in', $responseData)
            && (is_int($responseData['expires_in']) || is_numeric($responseData['expires_in']));

        if (! $hasAccessToken || ! $hasExpiresIn) {
            throw new Exception('API response missing or has invalid expected fields');
        }

        // TODO: Cache this response
        $token = $responseData['access_token'];
        $expiresIn = is_numeric($responseData['expires_in']) ? (int) $responseData['expires_in'] : 3600;

        // Only use scope if it's present and a string
        $scope = null;
        if (array_key_exists('scope', $responseData) && is_string($responseData['scope'])) {
            $scope = $responseData['scope'];
        }

        // Ensure token is a string as required by AccessToken constructor
        $tokenString = is_string($token) ? $token : '';

        return $this->accessToken = new AccessToken(
            token: $tokenString,
            expires: time() + $expiresIn,
            scope: $scope,
        );
    }

    /**
     * Get the authorization header with bearer token.
     *
     * @return string The authorization header value
     */
    public function getAuthorizationHeader(): string
    {
        return 'Bearer ' . (string) $this->getAccessToken();
    }
}
