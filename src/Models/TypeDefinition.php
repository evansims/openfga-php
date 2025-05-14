<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class TypeDefinition implements TypeDefinitionInterface
{
    /**
     * @param string                                $type      The type of the object that this definition is for.
     * @param null|TypeDefinitionRelationsInterface $relations An array of relation names to Userset definitions.
     * @param null|MetadataInterface                $metadata  An array whose keys are the name of the relation and whose value is the Metadata for that relation. It also holds information around the module name and source file if this model was constructed from a modular model.
     */
    public function __construct(
        private string $type,
        private ?TypeDefinitionRelationsInterface $relations = null,
        private ?MetadataInterface $metadata = null,
    ) {
    }

    public function getMetadata(): ?MetadataInterface
    {
        return $this->metadata;
    }

    public function getRelations(): ?TypeDefinitionRelationsInterface
    {
        return $this->relations;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function jsonSerialize(): array
    {
        $response = [
            'type' => $this->type,
        ];

        if ($this->getRelations()) {
            $response['relations'] = $this->getRelations()->jsonSerialize();
        }

        if ($this->getMetadata()) {
            $response['metadata'] = $this->getMetadata()->jsonSerialize();
        }

        return $response;
    }

    public static function fromArray(array $data): self
    {
        $data = self::validatedTypeDefinitionShape($data);

        return new self(
            type: $data['type'],
            relations: isset($data['relations']) ? TypeDefinitionRelations::fromArray($data['relations']) : null,
            metadata: isset($data['metadata']) ? Metadata::fromArray($data['metadata']) : null,
        );
    }

    /**
     * Validate the shape of the type definition array. Throws an exception if the data is invalid.
     *
     * @param array{type: string, relations?: TypeDefinitionRelationsShape, metadata?: MetadataShape} $data
     *
     * @throws InvalidArgumentException
     *
     * @return TypeDefinitionShape
     */
    public static function validatedTypeDefinitionShape(array $data): array
    {
        return $data;
    }
}
