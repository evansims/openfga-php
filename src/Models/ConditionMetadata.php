<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};

final class ConditionMetadata implements ConditionMetadataInterface
{
    private static ?SchemaInterface $schema = null;

    public function __construct(
        private string $module,
        private SourceInfoInterface $sourceInfo,
    ) {
    }

    public function getModule(): string
    {
        return $this->module;
    }

    public function getSourceInfo(): SourceInfoInterface
    {
        return $this->sourceInfo;
    }

    public function jsonSerialize(): array
    {
        return [
            'module' => $this->getModule(),
            'source_info' => $this->getSourceInfo()->jsonSerialize(),
        ];
    }

    public static function schema(): SchemaInterface
    {
        return self::$schema ??= new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'module', type: 'string', required: true),
                new SchemaProperty(name: 'source_info', type: SourceInfo::class, required: true),
            ],
        );
    }
}
