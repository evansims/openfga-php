<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Support\Http;

use Psr\Http\Message\{ResponseInterface, StreamInterface};

/**
 * Mock HTTP response implementation for testing PSR-7 HTTP message functionality.
 */
final class MockResponse implements ResponseInterface
{
    public function __construct(
        private int $statusCode,
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

    public function getProtocolVersion(): string
    {
        return '1.1';
    }

    public function getReasonPhrase(): string
    {
        return '';
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
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

    public function withoutHeader(string $name): static
    {
        return $this;
    }

    public function withProtocolVersion(string $version): static
    {
        return $this;
    }

    public function withStatus(int $code, string $reasonPhrase = ''): static
    {
        return $this;
    }
}
