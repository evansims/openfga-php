<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use Override;

/**
 * Represents a filter for limiting users by their relationships to specific object types.
 *
 * User type filters are used in queries to narrow down the set of users based on
 * their relationships to objects of particular types. This is particularly useful
 * when you want to find users who have specific permissions or roles related to
 * certain categories of resources.
 *
 * The filter can specify:
 * - A required object type that users must be related to
 * - An optional relation that further constrains the relationship type
 *
 * Examples:
 * - Find all users related to "document" objects
 * - Find all users who are "viewers" of "folder" objects
 * - Find all users who are "members" of "organization" objects
 *
 * @see https://openfga.dev/api/service#/Relationship%20Queries/ListUsers List Users API
 */
interface UserTypeFilterInterface extends ModelInterface
{
    /**
     * Get the optional relation filter for limiting user types.
     *
     * When specified, this filter limits the results to users that have
     * the specified relation to objects of the target type. This allows
     * for more specific filtering beyond just the object type.
     *
     * @return ?string The relation filter, or null if no relation filter is applied
     */
    public function getRelation(): ?string;

    /**
     * Get the object type to filter by.
     *
     * This specifies the type of objects that users should be related to
     * when filtering results. Only users connected to objects of this type
     * will be included in the filtered results.
     *
     * @return string The object type to filter by
     */
    public function getType(): string;

    /**
     * @return array<'relation'|'type', string>
     */
    #[Override]
    public function jsonSerialize(): array;
}
