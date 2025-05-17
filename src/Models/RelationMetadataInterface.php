<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @psalm-type RelationMetadataShape = array{module?: string, directly_related_user_types?: RelationReferencesShape, source_info?: SourceInfoShape}
 */
interface RelationMetadataInterface extends ModelInterface
{
    public function getDirectlyRelatedUserTypes(): ?RelationReferencesInterface;

    public function getModule(): ?string;

    public function getSourceInfo(): ?SourceInfoInterface;

    /**
     * @return RelationMetadataShape
     */
    public function jsonSerialize(): array;
}
