<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Models\Collections\RelationReferencesInterface;

interface RelationMetadataInterface extends ModelInterface
{
    /**
     * @return null|RelationReferencesInterface<RelationReferenceInterface>
     */
    public function getDirectlyRelatedUserTypes(): ?RelationReferencesInterface;

    public function getModule(): ?string;

    public function getSourceInfo(): ?SourceInfoInterface;

    /**
     * @return array{module?: string, directly_related_user_types?: array<string, array{type: string, relation?: string, wildcard?: object, condition?: string}>, source_info?: array{file?: string}}
     */
    public function jsonSerialize(): array;
}
