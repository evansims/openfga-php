<?php

declare(strict_types=1);

namespace OpenFGA\Network;

use Psr\Http\Message\StreamInterface;

final class RequestContext
{
    /**
     * @param RequestMethod         $method    The HTTP method.
     * @param string                $url       The URL.
     * @param ?StreamInterface      $body      The request body.
     * @param array<string, string> $headers   The request headers.
     * @param bool                  $useApiUrl Whether to use the API URL.
     */
    public function __construct(
        private RequestMethod $method,
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

    /**
     * @return array<string, string>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getMethod(): RequestMethod
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
