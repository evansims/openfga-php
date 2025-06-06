<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Support\Http;

use Psr\Http\Message\StreamInterface;

/**
 * Mock stream implementation for testing PSR-7 HTTP message functionality.
 */
final class MockStream implements StreamInterface
{
    public function __construct(private int $size)
    {
    }

    public function __toString(): string
    {
        return '';
    }

    public function close(): void
    {
    }

    public function detach()
    {
        return null;
    }

    public function eof(): bool
    {
        return true;
    }

    public function getContents(): string
    {
        return '';
    }

    public function getMetadata(?string $key = null)
    {
        return null;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function isReadable(): bool
    {
        return false;
    }

    public function isSeekable(): bool
    {
        return false;
    }

    public function isWritable(): bool
    {
        return false;
    }

    public function read(int $length): string
    {
        return '';
    }

    public function rewind(): void
    {
    }

    public function seek(int $offset, int $whence = SEEK_SET): void
    {
    }

    public function tell(): int
    {
        return 0;
    }

    public function write(string $string): int
    {
        return 0;
    }
}
