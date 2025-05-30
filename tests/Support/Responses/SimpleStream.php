<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Support\Responses;

use Exception;
use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use RuntimeException;

use function strlen;

final class SimpleStream implements StreamInterface
{
    private string $contents;

    private array $metadata = [];

    private int $position = 0;

    private bool $readable = true;

    private bool $seekable = true;

    private ?int $size = null;

    private bool $writable = true;

    public function __construct(string $contents = '')
    {
        $this->contents = $contents;
        $this->size = strlen($contents);
    }

    public function __toString(): string
    {
        try {
            if ($this->isSeekable()) {
                $this->seek(0);
            }

            return $this->getContents();
        } catch (Exception) {
            return '';
        }
    }

    public function close(): void
    {
        $this->readable = false;
        $this->writable = false;
        $this->seekable = false;
        $this->contents = '';
        $this->size = 0;
    }

    public function detach()
    {
        $this->close();

        return null;
    }

    public function eof(): bool
    {
        return $this->position >= $this->size;
    }

    public function getContents(): string
    {
        if (! $this->isReadable()) {
            throw new RuntimeException('Stream is not readable');
        }

        $result = substr($this->contents, $this->position);
        $this->position = $this->size;

        return $result;
    }

    public function getMetadata($key = null)
    {
        if (null === $key) {
            return $this->metadata;
        }

        return $this->metadata[$key] ?? null;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function isReadable(): bool
    {
        return $this->readable;
    }

    public function isSeekable(): bool
    {
        return $this->seekable;
    }

    public function isWritable(): bool
    {
        return $this->writable;
    }

    public function read($length): string
    {
        if (! $this->isReadable()) {
            throw new RuntimeException('Stream is not readable');
        }

        $result = substr($this->contents, $this->position, $length);
        $this->position += strlen($result);

        return $result;
    }

    public function rewind(): void
    {
        $this->seek(0);
    }

    public function seek($offset, $whence = SEEK_SET): void
    {
        if (! $this->isSeekable()) {
            throw new RuntimeException('Stream is not seekable');
        }

        $newPosition = match ($whence) {
            SEEK_SET => $offset,
            SEEK_CUR => $this->position + $offset,
            SEEK_END => $this->size + $offset,
            default => throw new InvalidArgumentException('Invalid whence'),
        };

        if (0 > $newPosition) {
            throw new RuntimeException('Unable to seek to negative position');
        }

        $this->position = $newPosition;
    }

    public function tell(): int
    {
        if (! $this->isSeekable()) {
            throw new RuntimeException('Stream is not seekable');
        }

        return $this->position;
    }

    public function write($string): int
    {
        if (! $this->isWritable()) {
            throw new RuntimeException('Stream is not writable');
        }

        $length = strlen($string);
        $this->contents = substr($this->contents, 0, $this->position) . $string . substr($this->contents, $this->position + $length);
        $this->position += $length;
        $this->size = strlen($this->contents);

        return $length;
    }
}
