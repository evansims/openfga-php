<?php

declare(strict_types=1);

namespace OpenFGA;

interface ConfigurationInterface
{
    /**
     * Get the API URL.
     *
     * @return null|string
     */
    public function getApiUrl(): ?string;

    /**
     * Get the authorization model ID.
     *
     * @return null|string
     */
    public function getAuthorizationModelId(): ?string;

    /**
     * Get the credential.
     *
     * @return null|Credentials\CredentialInterface
     */
    public function getCredential(): ?Credentials\CredentialInterface;

    /**
     * Get the HTTP client.
     *
     * @return null|\Psr\Http\Client\ClientInterface
     */
    public function getHttpClient(): ?\Psr\Http\Client\ClientInterface;

    /**
     * Get the HTTP response factory.
     *
     * @return null|\Psr\Http\Message\ResponseFactoryInterface
     */
    public function getHttpFactory(): ?\Psr\Http\Message\ResponseFactoryInterface;

    /**
     * Get the HTTP request factory.
     *
     * @return null|\Psr\Http\Message\RequestFactoryInterface
     */
    public function getHttpRequestFactory(): ?\Psr\Http\Message\RequestFactoryInterface;

    /**
     * Get the HTTP stream factory.
     *
     * @return null|\Psr\Http\Message\StreamFactoryInterface
     */
    public function getHttpStreamFactory(): ?\Psr\Http\Message\StreamFactoryInterface;

    /**
     * Get the store ID.
     *
     * @return null|string
     */
    public function getStoreId(): ?string;
}
