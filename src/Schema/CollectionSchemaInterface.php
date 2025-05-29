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
     * Get the wrapper key for the collection data if any.
     *
     * Some collections expect data wrapped in a specific key (e.g., Usersets uses 'child').
     *
     * @return null|string The wrapper key or null if data is not wrapped
     */
    public function getWrapperKey(): ?string;

    /**
     * Whether the collection requires at least one item.
     */
    public function requiresItems(): bool;
}
