<?php

declare(strict_types=1);

namespace OpenFGA\Schema;

interface CollectionSchemaInterface extends SchemaInterface
{
    /**
     * Get the type of each item in the collection.
     *
     * @return class-string
     */
    public function getItemType(): string;

    /**
     * Whether the collection requires at least one item.
     */
    public function requiresItems(): bool;
}
