<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface TypeDefinitionInterface extends ModelInterface
{
    public function getMetadata(): ?Metadata;

    public function getRelations(): ?array;

    public function getType(): string;
}
