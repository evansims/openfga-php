<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Models\Collections\TypeDefinitionRelationsInterface;

interface TypeDefinitionInterface extends ModelInterface
{
    public function getMetadata(): ?MetadataInterface;

    /**
     * @return null|TypeDefinitionRelationsInterface<UsersetInterface>
     */
    public function getRelations(): ?TypeDefinitionRelationsInterface;

    public function getType(): string;

    /**
     * @return array{type: string, relations?: array<int, array{computed_userset?: array{object?: string, relation?: string}, tuple_to_userset?: array{tupleset: array{object?: string, relation?: string}, computed_userset: array{object?: string, relation?: string}}, union?: array<mixed>, intersection?: array<mixed>, difference?: array{base: array<mixed>, subtract: array<mixed>}, direct?: object}>, metadata?: array{module?: string, relations?: array<string, mixed>, source_info?: array<string, mixed>}}
     */
    public function jsonSerialize(): array;
}
