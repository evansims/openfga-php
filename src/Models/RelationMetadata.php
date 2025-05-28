<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Models\Collections\{RelationReferences, RelationReferencesInterface};

use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};
use Override;

final class RelationMetadata implements RelationMetadataInterface
{
    public const OPENAPI_MODEL = 'RelationMetadata';

    private static ?SchemaInterface $schema = null;

    /**
     * @param null|string                                                  $module
     * @param null|RelationReferencesInterface<RelationReferenceInterface> $directlyRelatedUserTypes
     * @param null|SourceInfoInterface                                     $sourceInfo
     */
    public function __construct(
        private readonly ?string $module = null,
        private readonly ?RelationReferencesInterface $directlyRelatedUserTypes = null,
        private readonly ?SourceInfoInterface $sourceInfo = null,
    ) {
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getDirectlyRelatedUserTypes(): ?RelationReferencesInterface
    {
        return $this->directlyRelatedUserTypes;
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
                new SchemaProperty(name: 'directly_related_user_types', type: 'object', className: RelationReferences::class, required: false),
                new SchemaProperty(name: 'source_info', type: 'object', className: SourceInfo::class, required: false),
            ],
        );
    }
}
