<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Models\Collections\{RelationReferences, RelationReferencesInterface};
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};
use Override;

/**
 * Contains metadata information about a relation in your authorization model.
 *
 * RelationMetadata provides additional context about how a relation behaves,
 * including which user types can be directly assigned to it and source
 * information for debugging. This helps with model validation and provides
 * insights into your authorization structure.
 *
 * Use this when you need to understand the constraints and properties of
 * specific relations in your authorization model.
 */
final class RelationMetadata implements RelationMetadataInterface
{
    public const string OPENAPI_MODEL = 'RelationMetadata';

    private static ?SchemaInterface $schema = null;

    /**
     * @param string|null                                                  $module
     * @param RelationReferencesInterface<RelationReferenceInterface>|null $directlyRelatedUserTypes
     * @param SourceInfoInterface|null                                     $sourceInfo
     */
    public function __construct(
        private readonly ?string $module = null,
        private readonly ?RelationReferencesInterface $directlyRelatedUserTypes = null,
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
                new SchemaProperty(name: 'directly_related_user_types', type: 'object', className: RelationReferences::class, required: false),
                new SchemaProperty(name: 'source_info', type: 'object', className: SourceInfo::class, required: false),
            ],
        );
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getDirectlyRelatedUserTypes(): ?RelationReferencesInterface
    {
        return $this->directlyRelatedUserTypes;
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

        if ($this->directlyRelatedUserTypes instanceof RelationReferencesInterface) {
            $result['directly_related_user_types'] = $this->directlyRelatedUserTypes->jsonSerialize();
        }

        if ($this->sourceInfo instanceof SourceInfoInterface) {
            $result['source_info'] = $this->sourceInfo->jsonSerialize();
        }

        return $result;
    }
}
