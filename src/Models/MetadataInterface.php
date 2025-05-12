<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface MetadataInterface extends ModelInterface
{
    public function getModule(): ?string;

    public function getRelations(): ?RelationReferencesInterface;

    public function getSourceInfo(): ?SourceInfoInterface;
}
