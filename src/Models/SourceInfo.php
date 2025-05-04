<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class SourceInfo extends Model implements SourceInfoInterface
{
    public function __construct(
        public string $file,
    ) {
    }

    public function toArray(): array
    {
        return [
            'file' => $this->file,
        ];
    }

    public static function fromArray(array $data): self
    {
        $file = $data['file'] ?? null;

        $file = $file ? (string)$file : null;

        return new self(
            file: $file,
        );
    }
}
