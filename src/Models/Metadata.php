<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Models\Collections\RelationMetadataCollection;
use OpenFGA\Schemas\{Schema, SchemaInterface, SchemaProperty};
use Override;

/**
 * Contains metadata information about type definitions in your authorization model.
 *
 * Metadata provides additional context about how your authorization types behave,
 * including module information, relation constraints, and source details.
 * This information helps with model validation, debugging, and understanding
 * the structure of your authorization system.
 *
 * Use this when you need insights into the properties and constraints of
 * your authorization model's type definitions.
 */
final class Metadata implements MetadataInterface
{
    public const string OPENAPI_MODEL = 'Metadata';

    private static ?SchemaInterface $schema = null;

    /**
     * Create new metadata for a type definition.
     *
     * @param string|null                     $module     Optional module name for organization
     * @param RelationMetadataCollection|null $relations  Optional collection of relation metadata
     * @param SourceInfoInterface|null        $sourceInfo Optional source information for debugging
     */
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
}
