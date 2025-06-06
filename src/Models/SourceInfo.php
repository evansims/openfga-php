<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use InvalidArgumentException;
use OpenFGA\Exceptions\{ClientError, ClientThrowable};
use OpenFGA\Messages;
use OpenFGA\Schemas\{Schema, SchemaInterface, SchemaProperty};
use OpenFGA\Translation\Translator;
use Override;
use ReflectionException;

/**
 * Represents source file information for debugging and development tools.
 *
 * SourceInfo provides metadata about where elements of your authorization
 * model were originally defined, including file paths. This information
 * is valuable for development tools, error reporting, and debugging
 * authorization model issues.
 *
 * Use this when you need to trace authorization model elements back to
 * their source definitions for debugging or tooling purposes.
 */
final class SourceInfo implements SourceInfoInterface
{
    public const string OPENAPI_MODEL = 'SourceInfo';

    private static ?SchemaInterface $schema = null;

    /**
     * @param string $file The source file path or name
     *
     * @throws ClientThrowable          If the file parameter is empty
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    public function __construct(
        private readonly string $file,
    ) {
        if ('' === $this->file) {
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::MODEL_SOURCE_INFO_FILE_EMPTY)]);
        }
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public static function schema(): SchemaInterface
    {
        return self::$schema ??= new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'file', type: 'string', required: true),
            ],
        );
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getFile(): string
    {
        return $this->file;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return [
            'file' => $this->file,
        ];
    }
}
