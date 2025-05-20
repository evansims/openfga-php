<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface MetadataInterface extends ModelInterface
{
    public function getModule(): ?string;

    public function getRelations(): ?RelationMetadataInterface;

    public function getSourceInfo(): ?SourceInfoInterface;

    /**
     * @return array{
     *     module?: string,
     *     relations?: array<string, mixed>,
     *     source_info?: array<string, mixed>,
     * }
     */
    public function jsonSerialize(): array;
}
