<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Support\Http;

use Psr\Http\Message\{RequestInterface, StreamInterface, UriInterface};

/**
 * Mock HTTP request implementation for testing PSR-7 HTTP message functionality.
 */
final class MockRequest implements RequestInterface
{
    public function __construct(
        private string $method,
        private string $uri,
        private int $bodySize = 0,
    ) {
    }

    public function getBody(): StreamInterface
    {
        return new MockStream($this->bodySize);
    }

    public function getHeader(string $name): array
    {
        return [];
    }

    public function getHeaderLine(string $name): string
    {
        return '';
    }

    public function getHeaders(): array
    {
        return [];
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getProtocolVersion(): string
    {
        return '1.1';
    }

    public function getRequestTarget(): string
    {
        return '/';
    }

    public function getUri(): UriInterface
    {
        return new MockUri($this->uri);
    }

    public function hasHeader(string $name): bool
    {
        return false;
    }

    public function withAddedHeader(string $name, $value): static
    {
        return $this;
    }

    public function withBody(StreamInterface $body): static
    {
        return $this;
    }

    public function withHeader(string $name, $value): static
    {
        return $this;
    }

    public function withMethod(string $method): static
    {
        return $this;
    }

    public function withoutHeader(string $name): static
    {
        return $this;
    }

    public function withProtocolVersion(string $version): static
    {
        return $this;
    }

    public function withRequestTarget(string $requestTarget): static
    {
        return $this;
    }

    public function withUri(UriInterface $uri, bool $preserveHost = false): static
    {
        return $this;
    }
}
