<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @psalm-type MetadataShape = array{module?: string, relations?: RelationMetadataShape, source_info?: SourceInfoShape}
 */
interface MetadataInterface extends ModelInterface
{
    public function getModule(): ?string;

    public function getRelations(): ?RelationMetadataInterface;

    public function getSourceInfo(): ?SourceInfoInterface;

    /**
     * @return MetadataShape
     */
    public function jsonSerialize(): array;

    /**
     * @param MetadataShape $data
     */
    public static function fromArray(array $data): static;
}
