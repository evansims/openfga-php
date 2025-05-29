<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use Override;

interface MetadataInterface extends ModelInterface
{
    public function getModule(): ?string;

    public function getRelations(): ?Collections\RelationMetadataCollection;

    public function getSourceInfo(): ?SourceInfoInterface;

    /**
     * @return array{module?: string, relations?: array<string, mixed>, source_info?: array{file?: string}}
     */
    #[Override]
    public function jsonSerialize(): array;
}
