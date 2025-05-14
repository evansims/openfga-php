<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class SourceInfo implements SourceInfoInterface
{
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
            'file' => $this->getFile(),
        ];
    }

    public static function fromArray(array $data): self
    {
        $data = self::validatedSourceInfoShape($data);

        return new self(
            file: $data['file'],
        );
    }

    /**
     * Validates the shape of the array to be used as source info data. Throws an exception if the data is invalid.
     *
     * @param array{file: string} $data
     *
     * @throws InvalidArgumentException
     *
     * @return SourceInfoShape
     */
    public static function validatedSourceInfoShape(array $data): array
    {
        return $data;
    }
}
