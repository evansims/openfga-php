<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Models\Collections\{TypeDefinitionRelations, TypeDefinitionRelationsInterface};

use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};
use Override;

final class TypeDefinition implements TypeDefinitionInterface
{
    public const OPENAPI_MODEL = 'TypeDefinition';

    private static ?SchemaInterface $schema = null;

    /**
     * @param string                                                  $type      The type of the object that this definition is for.
     * @param null|TypeDefinitionRelationsInterface<UsersetInterface> $relations An array of relation names to Userset definitions.
     * @param null|MetadataInterface                                  $metadata  An array whose keys are the name of the relation and whose value is the Metadata for that relation. It also holds information around the module name and source file if this model was constructed from a modular model.
     */
    public function __construct(
        private readonly string $type,
        private readonly ?TypeDefinitionRelationsInterface $relations = null,
        private readonly ?MetadataInterface $metadata = null,
    ) {
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getMetadata(): ?MetadataInterface
    {
        return $this->metadata;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getRelations(): ?TypeDefinitionRelationsInterface
    {
        return $this->relations;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return $this->type;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return array_filter([
            'type' => $this->type,
            'relations' => $this->relations?->jsonSerialize(),
            'metadata' => $this->metadata?->jsonSerialize(),
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
                new SchemaProperty(name: 'type', type: 'string', required: true),
                new SchemaProperty(name: 'relations', type: 'object', className: TypeDefinitionRelations::class, required: false),
                new SchemaProperty(name: 'metadata', type: 'object', className: Metadata::class, required: false),
            ],
        );
    }
}
