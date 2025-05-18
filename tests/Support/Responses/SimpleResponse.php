<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Support\Responses;

use Psr\Http\Message\{ResponseInterface, StreamInterface};

final class SimpleResponse implements ResponseInterface
{
    private StreamInterface $body;

    private array $headers = [];

    private string $protocolVersion = '1.1';

    private string $reasonPhrase = '';

    public function __construct(
        private int $statusCode = 200,
        string | StreamInterface $body = '',
        array $headers = [],
        ?string $version = null,
    ) {
        $this->body = $body instanceof StreamInterface ? $body : new SimpleStream($body);

        if (null !== $version) {
            $this->protocolVersion = $version;
        }

        foreach ($headers as $header => $value) {
            $this->headers[$header] = (array) $value;
        }
    }

    public function getBody(): StreamInterface
    {
        return $this->body;
    }

    public function getHeader($name): array
    {
        $name = strtolower($name);

        foreach ($this->headers as $header => $value) {
            if (strtolower($header) === $name) {
                return (array) $value;
            }
        }

        return [];
    }

    public function getHeaderLine($name): string
    {
        return implode(', ', $this->getHeader($name));
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    public function getReasonPhrase(): string
    {
        return $this->reasonPhrase;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function hasHeader($name): bool
    {
        $name = strtolower($name);

        foreach ($this->headers as $header => $value) {
            if (strtolower($header) === $name) {
                return true;
            }
        }

        return false;
    }

    public function withAddedHeader($name, $value): self
    {
        $new = clone $this;
        $new->headers[$name] = array_merge(
            $new->getHeader($name),
            (array) $value,
        );

        return $new;
    }

    public function withBody(StreamInterface $body): self
    {
        if ($body === $this->body) {
            return $this;
        }

        $new = clone $this;
        $new->body = $body;

        return $new;
    }

    public function withHeader($name, $value): self
    {
        $new = clone $this;
        $new->headers[$name] = (array) $value;

        return $new;
    }

    public function withoutHeader($name): self
    {
        $new = clone $this;

        foreach ($new->headers as $header => $value) {
            if (strtolower($header) === strtolower($name)) {
                unset($new->headers[$header]);
            }
        }

        return $new;
    }

    public function withProtocolVersion($version): self
    {
        if ($version === $this->protocolVersion) {
            return $this;
        }

        $new = clone $this;
        $new->protocolVersion = (string) $version;

        return $new;
    }

    public function withStatus($code, $reasonPhrase = ''): self
    {
        $code = (int) $code;

        if ($code === $this->statusCode && $reasonPhrase === $this->reasonPhrase) {
            return $this;
        }

        $new = clone $this;
        $new->statusCode = $code;
        $new->reasonPhrase = $reasonPhrase;

        return $new;
    }
}
