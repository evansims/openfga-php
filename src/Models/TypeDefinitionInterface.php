<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Models\Collections\{TypeDefinitionRelationsInterface};

interface TypeDefinitionInterface extends ModelInterface
{
    public function getMetadata(): ?MetadataInterface;

    /**
     * @return null|TypeDefinitionRelationsInterface<UsersetInterface>
     */
    public function getRelations(): ?TypeDefinitionRelationsInterface;

    public function getType(): string;

    /**
     * @return array{
     *     type: string,
     *     relations?: array<string, mixed>,
     *     metadata?: array<string, mixed>,
     * }
     */
    public function jsonSerialize(): array;
}
