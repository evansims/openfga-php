<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use InvalidArgumentException;
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};
use Override;

final class SourceInfo implements SourceInfoInterface
{
    public const OPENAPI_MODEL = 'SourceInfo';

    private static ?SchemaInterface $schema = null;

    public function __construct(
        private readonly string $file,
    ) {
        if ('' === $this->file) {
            throw new InvalidArgumentException('SourceInfo::$file cannot be empty.');
        }
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getFile(): string
    {
        return $this->file;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return [
            'file' => $this->file,
        ];
    }

    #[Override]
    /**
     * @inheritDoc
     */
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
