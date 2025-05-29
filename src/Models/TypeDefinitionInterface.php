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
     * @return array{
     *     type: string,
     *     relations: array<string, array{
     *         computedUserset?: array{object?: string, relation?: string},
     *         tupleToUserset?: array{tupleset: array{object?: string, relation?: string},
     *         computedUserset: array{object?: string, relation?: string}},
     *         union?: array<mixed>,
     *         intersection?: array<mixed>,
     *         difference?: array{base: array<mixed>, subtract: array<mixed>},
     *         this?: object
     *     }>|object,
     *     metadata?: array{module?: string, relations?: array<string, mixed>, source_info?: array{file?: string}}
     * }
     */
    #[Override]
    public function jsonSerialize(): array;
}
