<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Support\Http;

use Psr\Http\Message\UriInterface;

/**
 * Mock URI implementation for testing PSR-7 HTTP message functionality.
 */
final class MockUri implements UriInterface
{
    public function __construct(private string $uri)
    {
    }

    public function __toString(): string
    {
        return $this->uri;
    }

    public function getAuthority(): string
    {
        return 'api.openfga.example';
    }

    public function getFragment(): string
    {
        return '';
    }

    public function getHost(): string
    {
        return 'api.openfga.example';
    }

    public function getPath(): string
    {
        return '/stores/store-123/check';
    }

    public function getPort(): ?int
    {
        return null;
    }

    public function getQuery(): string
    {
        return '';
    }

    public function getScheme(): string
    {
        return 'https';
    }

    public function getUserInfo(): string
    {
        return '';
    }

    public function withFragment(string $fragment): UriInterface
    {
        return $this;
    }

    public function withHost(string $host): UriInterface
    {
        return $this;
    }

    public function withPath(string $path): UriInterface
    {
        return $this;
    }

    public function withPort(?int $port): UriInterface
    {
        return $this;
    }

    public function withQuery(string $query): UriInterface
    {
        return $this;
    }

    public function withScheme(string $scheme): UriInterface
    {
        return $this;
    }

    public function withUserInfo(string $user, ?string $password = null): UriInterface
    {
        return $this;
    }
}
