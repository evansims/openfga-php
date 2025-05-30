<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Models\Collections\RelationMetadataCollection;
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};

use Override;

final class Metadata implements MetadataInterface
{
    private static ?SchemaInterface $schema = null;

    public function __construct(
        private readonly ?string $module = null,
        private readonly ?RelationMetadataCollection $relations = null,
        private readonly ?SourceInfoInterface $sourceInfo = null,
    ) {
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getModule(): ?string
    {
        return $this->module;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getRelations(): ?RelationMetadataCollection
    {
        return $this->relations;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getSourceInfo(): ?SourceInfoInterface
    {
        return $this->sourceInfo;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function jsonSerialize(): array
    {
        $result = [];

        if (null !== $this->module && '' !== $this->module) {
            $result['module'] = $this->module;
        }

        if ($this->relations instanceof RelationMetadataCollection) {
            $result['relations'] = $this->relations->jsonSerialize();
        }

        if ($this->sourceInfo instanceof SourceInfoInterface) {
            $result['source_info'] = $this->sourceInfo->jsonSerialize();
        }

        return $result;
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
                new SchemaProperty(name: 'module', type: 'string', required: false),
                new SchemaProperty(name: 'relations', type: 'object', className: RelationMetadataCollection::class, required: false),
                new SchemaProperty(name: 'source_info', type: 'object', className: SourceInfo::class, required: false),
            ],
        );
    }
}
