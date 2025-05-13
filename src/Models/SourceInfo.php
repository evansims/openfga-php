<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class SourceInfo implements SourceInfoInterface
{
    use ModelTrait;

    public function __construct(
        private string $file,
    ) {
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function jsonSerialize(): array
    {
        return [
            'file' => $this->file,
        ];
    }

    public static function fromArray(array $data): self
    {
        $file = $data['file'] ?? null;

        $file = $file ? (string) $file : null;

        return new self(
            file: $file,
        );
    }
}
