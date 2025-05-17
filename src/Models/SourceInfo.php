<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use InvalidArgumentException;
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};

final class SourceInfo implements SourceInfoInterface
{
    private static ?SchemaInterface $schema = null;

    public function __construct(
        private readonly string $file,
    ) {
        if ('' === $this->file) {
            throw new InvalidArgumentException('SourceInfo::$file cannot be empty.');
        }
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

    public static function schema(): SchemaInterface
    {
        return self::$schema ??= new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'file', type: 'string', required: true),
            ],
        );
    }
}
