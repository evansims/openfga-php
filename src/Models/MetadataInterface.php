<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use Override;

interface MetadataInterface extends ModelInterface
{
    public function getModule(): ?string;

    public function getRelations(): ?RelationMetadataInterface;

    public function getSourceInfo(): ?SourceInfoInterface;

    /**
     * @return array<'module'|'relations'|'source_info', array{directly_related_user_types?: array<string, array{condition?: string, relation?: string, type: string, wildcard?: object}>, file?: string, module?: string, source_info?: array{file?: string}}|string>
     */
    #[Override]
    public function jsonSerialize(): array;
}
