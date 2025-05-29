<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};

use Override;

final class ConditionMetadata implements ConditionMetadataInterface
{
    public const OPENAPI_MODEL = 'ConditionMetadata';

    private static ?SchemaInterface $schema = null;

    public function __construct(
        private readonly string $module,
        private readonly SourceInfoInterface $sourceInfo,
    ) {
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getModule(): string
    {
        return $this->module;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getSourceInfo(): SourceInfoInterface
    {
        return $this->sourceInfo;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return [
            'module' => $this->module,
            'source_info' => $this->sourceInfo->jsonSerialize(),
        ];
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
                new SchemaProperty(name: 'module', type: 'string', required: true),
                new SchemaProperty(name: 'source_info', type: 'object', className: SourceInfo::class, required: true),
            ],
        );
    }
}
