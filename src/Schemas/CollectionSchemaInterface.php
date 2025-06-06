<?php

declare(strict_types=1);

namespace OpenFGA\Schemas;

use InvalidArgumentException;
use OpenFGA\Exceptions\ClientThrowable;

/**
 * Interface for collection schema definitions in the OpenFGA system.
 *
 * This interface extends the base SchemaInterface to provide specialized validation
 * and structure definitions for collections of objects. Collection schemas handle
 * arrays and lists of objects that conform to specific types, with support for
 * wrapper keys and item requirements.
 *
 * Collection schemas are essential for validating complex data structures like
 * lists of users, authorization models, relationship tuples, and other grouped
 * data returned by the OpenFGA API.
 *
 * Examples of collections include Users, AuthorizationModels, Tuples, and other
 * array-based response data that require consistent validation and type safety.
 *
 * @see SchemaInterface Base schema interface for single object validation
 * @see https://openfga.dev/docs/concepts OpenFGA core concepts including relationship tuples and authorization models
 */
interface CollectionSchemaInterface extends SchemaInterface
{
    /**
     * Get the type of each item in the collection.
     *
     * @throws InvalidArgumentException If the item type is invalid
     * @throws ClientThrowable          If the item type cannot be determined
     *
     * @return class-string
     */
    public function getItemType(): string;

    /**
     * Get the wrapper key for the collection data if any.
     *
     * Some collections expect data wrapped in a specific key (for example, Usersets uses 'child').
     *
     * @return string|null The wrapper key or null if data is not wrapped
     */
    public function getWrapperKey(): ?string;

    /**
     * Whether the collection requires at least one item.
     *
     * @return bool True if the collection must contain at least one item, false if empty collections are allowed
     */
    public function requiresItems(): bool;
}
