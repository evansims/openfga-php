<?php

declare(strict_types=1);

namespace OpenFGA\SDK\Authentication;

use Exception;
use OpenFGA\SDK\Configuration\ClientConfigurationInterface;
use OpenFGA\SDK\Utilities\Network;
use OpenFGA\SDK\Utilities\RequestBodyFormat;

final class Authentication
{
    public function __construct(
        public ClientConfigurationInterface $configuration,
        public ?AccessToken $accessToken = null,
    ) {
    }

    public function obtainToken() {
        $network = new Network();

        $body = $network->createRequestBody([
            'grant_type' => 'client_credentials',
            'client_id' => $this->configuration->clientId,
            'client_secret' => $this->configuration->clientSecret,
            'audience' => $this->configuration->audience,
        ], RequestBodyFormat::POST);

        $request = $network->getHttpRequestFactory()->createRequest(
            'POST',
            "{$this->configuration->apiUrl}/oauth2/token",
            [
                'User-Agent' => $network->getUserAgent(),
                'Accept' => 'application/json',
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            $body
        );

        try {
            $response = $network->getHttpClient()->sendRequest($request);
        } catch (Exception $e) {
            throw new Exception('API request issuance failed');
        }

        if (200 !== $response->getStatusCode()) {
            throw new Exception('API request failed');
        }

        return json_decode($response->getBody()->getContents(), true);
    }
}
