<?php

declare(strict_types=1);

namespace OpenFGA\Schema;

interface CollectionSchemaInterface extends SchemaInterface
{
    public function getItemType(): string;

    public function requiresItems(): bool;
}
