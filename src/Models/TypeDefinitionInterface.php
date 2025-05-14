<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @psalm-type TypeDefinitionShape = array{type: string, relations?: TypeDefinitionRelationsShape, metadata?: MetadataShape}
 */
interface TypeDefinitionInterface extends ModelInterface
{
    public function getMetadata(): ?MetadataInterface;

    public function getRelations(): ?TypeDefinitionRelationsInterface;

    public function getType(): string;

    /**
     * @return TypeDefinitionShape
     */
    public function jsonSerialize(): array;

    /**
     * @param TypeDefinitionShape $data
     */
    public static function fromArray(array $data): static;
}
