<?php

declare(strict_types=1);

use OpenFGA\Exceptions\ApiUnauthenticatedException;
use OpenFGA\Responses\ResponseTrait;
use Psr\Http\Message\{ResponseInterface, StreamInterface};

final class DummyResponse
{
    use ResponseTrait;
}

final class SimpleStream implements StreamInterface
{
    public function __construct(private string $contents) {}

    public function __toString(): string { return $this->contents; }
    public function close(): void {}
    public function detach() { return null; }
    public function getSize(): ?int { return strlen($this->contents); }
    public function tell(): int { return 0; }
    public function eof(): bool { return true; }
    public function isSeekable(): bool { return false; }
    public function seek($offset, $whence = SEEK_SET): void {}
    public function rewind(): void {}
    public function isWritable(): bool { return false; }
    public function write($string): int { return 0; }
    public function isReadable(): bool { return false; }
    public function read($length): string { return ''; }
    public function getContents(): string { return $this->contents; }
    public function getMetadata($key = null) { return null; }
}

final class SimpleResponse implements ResponseInterface
{
    public function __construct(private int $statusCode, private string $body = '') {}

    public function getProtocolVersion(): string { return '1.1'; }
    public function withProtocolVersion($version): static { return $this; }
    public function getHeaders(): array { return []; }
    public function hasHeader($name): bool { return false; }
    public function getHeader($name): array { return []; }
    public function getHeaderLine($name): string { return ''; }
    public function withHeader($name, $value): static { return $this; }
    public function withAddedHeader($name, $value): static { return $this; }
    public function withoutHeader($name): static { return $this; }
    public function getBody(): StreamInterface { return new SimpleStream($this->body); }
    public function withBody(StreamInterface $body): static { return $this; }
    public function getStatusCode(): int { return $this->statusCode; }
    public function withStatus($code, $reasonPhrase = ''): static { $this->statusCode = $code; return $this; }
    public function getReasonPhrase(): string { return ''; }
}

test('handleResponseException throws ApiUnauthenticatedException for 401', function (): void {
    $response = new SimpleResponse(401, 'unauthenticated');

    $this->expectException(ApiUnauthenticatedException::class);
    DummyResponse::handleResponseException($response);
});
