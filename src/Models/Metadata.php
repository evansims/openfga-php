<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};

final class Metadata implements MetadataInterface
{
    private static ?SchemaInterface $schema = null;

    public function __construct(
        private ?string $module = null,
        private ?RelationMetadataInterface $relations = null,
        private ?SourceInfoInterface $sourceInfo = null,
    ) {
    }

    public function getModule(): ?string
    {
        return $this->module;
    }

    public function getRelations(): ?RelationMetadataInterface
    {
        return $this->relations;
    }

    public function getSourceInfo(): ?SourceInfoInterface
    {
        return $this->sourceInfo;
    }

    public function jsonSerialize(): array
    {
        $response = [];

        if (null !== $this->getModule()) {
            $response['module'] = $this->getModule();
        }

        if (null !== $this->getRelations()) {
            $response['relations'] = $this->getRelations()->jsonSerialize();
        }

        if (null !== $this->getSourceInfo()) {
            $response['source_info'] = $this->getSourceInfo()->jsonSerialize();
        }

        return $response;
    }

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
