<?php

declare(strict_types=1);

namespace OpenFGA;

use OpenFGA\Credentials\CredentialInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\{RequestFactoryInterface, ResponseFactoryInterface, StreamFactoryInterface};

final class Configuration implements ConfigurationInterface
{
    public function __construct(
        public ?string $apiUrl = null,
        public ?CredentialInterface $credential = null,
        public ?ClientInterface $httpClient = null,
        public ?ResponseFactoryInterface $httpFactory = null,
        public ?StreamFactoryInterface $httpStreamFactory = null,
        public ?RequestFactoryInterface $httpRequestFactory = null,
    ) {
    }

    /**
     * Get the API URL.
     *
     * @return null|string
     */
    public function getApiUrl(): ?string
    {
        return $this->apiUrl;
    }

    /**
     * Get the credential.
     *
     * @return null|CredentialInterface
     */
    public function getCredential(): ?CredentialInterface
    {
        return $this->credential;
    }

    /**
     * Get the HTTP client.
     *
     * @return null|ClientInterface
     */
    public function getHttpClient(): ?ClientInterface
    {
        return $this->httpClient;
    }

    /**
     * Get the HTTP response factory.
     *
     * @return null|ResponseFactoryInterface
     */
    public function getHttpFactory(): ?ResponseFactoryInterface
    {
        return $this->httpFactory;
    }

    /**
     * Get the HTTP request factory.
     *
     * @return null|RequestFactoryInterface
     */
    public function getHttpRequestFactory(): ?RequestFactoryInterface
    {
        return $this->httpRequestFactory;
    }

    /**
     * Get the HTTP stream factory.
     *
     * @return null|StreamFactoryInterface
     */
    public function getHttpStreamFactory(): ?StreamFactoryInterface
    {
        return $this->httpStreamFactory;
    }
}
