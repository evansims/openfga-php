<?php

declare(strict_types=1);

namespace OpenFGA\Credentials;

use function is_string;

final class ClientCredential extends Credential implements ClientCredentialInterface
{
    /**
     * @param array<string, mixed> $configuration Configuration options
     * @param null|string          $apiIssuer     The API issuer
     * @param null|string          $apiAudience   The API audience
     * @param null|string          $clientId      The client ID
     * @param null|string          $clientSecret  The client secret
     */
    public function __construct(
        private readonly array $configuration = [],
        private ?string $apiIssuer = null,
        private ?string $apiAudience = null,
        private ?string $clientId = null,
        private ?string $clientSecret = null,
    ) {
        // If any parameters are not provided but exist in configuration, use those values
        if (null === $this->apiIssuer && isset($configuration['api_issuer']) && is_string($configuration['api_issuer'])) {
            $this->apiIssuer = $configuration['api_issuer'];
        }

        if (null === $this->apiAudience && isset($configuration['api_audience']) && is_string($configuration['api_audience'])) {
            $this->apiAudience = $configuration['api_audience'];
        }

        if (null === $this->clientId && isset($configuration['client_id']) && is_string($configuration['client_id'])) {
            $this->clientId = $configuration['client_id'];
        }

        if (null === $this->clientSecret && isset($configuration['client_secret']) && is_string($configuration['client_secret'])) {
            $this->clientSecret = $configuration['client_secret'];
        }
    }

    public function getApiAudience(): ?string
    {
        return $this->apiAudience;
    }

    public function getApiIssuer(): ?string
    {
        return $this->apiIssuer;
    }

    public function getClientId(): ?string
    {
        return $this->clientId;
    }

    public function getClientSecret(): ?string
    {
        return $this->clientSecret;
    }

    /**
     * Get the configuration array.
     *
     * @return array<string, mixed>
     */
    public function getConfiguration(): array
    {
        return $this->configuration;
    }
}
