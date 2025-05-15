<?php

declare(strict_types=1);

namespace OpenFGA\Network;

use Psr\Http\Message\StreamInterface;

final class RequestContext
{
    public function __construct(
        private NetworkRequestMethod $method,
        private string $url,
        private ?StreamInterface $body = null,
        private array $headers = [],
        private bool $useApiUrl = true,
    ) {
    }

    public function getBody(): ?StreamInterface
    {
        return $this->body;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getMethod(): NetworkRequestMethod
    {
        return $this->method;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function useApiUrl(): bool
    {
        return $this->useApiUrl;
    }
}
