<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Models\Collections\{RelationReferences, RelationReferencesInterface};
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};

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

    public function getDirectlyRelatedUserTypes(): ?RelationReferencesInterface
    {
        return $this->directlyRelatedUserTypes;
    }

    public function getModule(): ?string
    {
        return $this->module;
    }

    public function getSourceInfo(): ?SourceInfoInterface
    {
        return $this->sourceInfo;
    }

    public function jsonSerialize(): array
    {
        return array_filter([
            'module' => $this->module,
            'directly_related_user_types' => $this->directlyRelatedUserTypes?->jsonSerialize(),
            'source_info' => $this->sourceInfo?->jsonSerialize(),
        ], static fn ($value): bool => null !== $value);
    }

    public static function schema(): SchemaInterface
    {
        return self::$schema ??= new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'module', type: 'string', required: false),
                new SchemaProperty(name: 'directly_related_user_types', type: RelationReferences::class, required: false),
                new SchemaProperty(name: 'source_info', type: SourceInfo::class, required: false),
            ],
        );
    }
}
