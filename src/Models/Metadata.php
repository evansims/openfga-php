<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};

use Override;

final class Metadata implements MetadataInterface
{
    private static ?SchemaInterface $schema = null;

    public function __construct(
        private readonly ?string $module = null,
        private readonly ?RelationMetadataInterface $relations = null,
        private readonly ?SourceInfoInterface $sourceInfo = null,
    ) {
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getModule(): ?string
    {
        return $this->module;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getRelations(): ?RelationMetadataInterface
    {
        return $this->relations;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getSourceInfo(): ?SourceInfoInterface
    {
        return $this->sourceInfo;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return array_filter([
            'module' => $this->module,
            'relations' => $this->relations?->jsonSerialize(),
            'source_info' => $this->sourceInfo?->jsonSerialize(),
        ], static fn ($value): bool => null !== $value);
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
                new SchemaProperty(name: 'module', type: 'string', required: false),
                new SchemaProperty(name: 'relations', type: RelationMetadata::class, required: false),
                new SchemaProperty(name: 'source_info', type: SourceInfo::class, required: false),
            ],
        );
    }
}
