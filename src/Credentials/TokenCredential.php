<?php

declare(strict_types=1);

namespace OpenFGA\Credentials;

use function is_string;

final class TokenCredential extends Credential implements TokenCredentialInterface
{
    /**
     * @param array<string, mixed> $configuration Configuration options
     * @param null|string          $apiToken      The API token
     */
    public function __construct(
        private readonly array $configuration = [],
        private ?string $apiToken = null,
    ) {
        // If token is not provided but exists in configuration, use that value
        if (null === $this->apiToken && isset($configuration['api_token']) && is_string($configuration['api_token'])) {
            $this->apiToken = $configuration['api_token'];
        }
    }

    /**
     * Get the API token.
     *
     * @return null|string
     */
    public function getApiToken(): ?string
    {
        return $this->apiToken;
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
