<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Models\Collections\TypeDefinitionRelationsInterface;
use Override;

interface TypeDefinitionInterface extends ModelInterface
{
    public function getMetadata(): ?MetadataInterface;

    /**
     * @return null|TypeDefinitionRelationsInterface<UsersetInterface>
     */
    public function getRelations(): ?TypeDefinitionRelationsInterface;

    public function getType(): string;

    /**
     * @return array{type: string, relations?: array<string, array{computed_userset?: array{object?: string, relation?: string}, tuple_to_userset?: array{tupleset: array{object?: string, relation?: string}, computed_userset: array{object?: string, relation?: string}}, union?: array<mixed>, intersection?: array<mixed>, difference?: array{base: array<mixed>, subtract: array<mixed>}, direct?: object}>, metadata?: array<'module'|'relations'|'source_info', array{directly_related_user_types?: array<string, array{condition?: string, relation?: string, type: string, wildcard?: object}>, file?: string, module?: string, source_info?: array{file?: string}}|string>}
     */
    #[Override]
    public function jsonSerialize(): array;
}
