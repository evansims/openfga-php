<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use Override;

/**
 * Represents a user in an OpenFGA authorization model.
 *
 * In OpenFGA, users are flexible entities that can represent various types of subjects
 * in authorization relationships. The user concept extends beyond individual people to
 * include groups, roles, services, or any entity that can be granted permissions.
 * OpenFGA supports multiple user representation patterns to accommodate complex
 * authorization scenarios:
 *
 * **Direct User Objects**: Simple user identifiers in "type:id" format, such as
 * "user:alice" or "service:backup-agent." These represent concrete entities with
 * specific identities that can be directly assigned permissions.
 *
 * **Usersets**: Dynamic user groups defined through relationships, such as "all
 * editors of document:readme" or "all members of group:engineering." Usersets
 * enable permissions that automatically adapt as relationships change.
 *
 * **Wildcards**: Type-based user groups that match all users of a specific type,
 * such as "all users of type employee." Wildcards enable broad, organization-wide
 * permissions without enumerating individual users.
 *
 * **Difference Operations**: Complex user definitions that include some users
 * while excluding others, such as "all editors except contractors." This enables
 * fine-grained access control with exception handling.
 *
 * This flexible user model enables OpenFGA to handle sophisticated authorization
 * patterns while maintaining performance and simplicity in common use cases.
 *
 * @see https://openfga.dev/docs/concepts#what-is-a-user OpenFGA User Concepts
 * @see https://openfga.dev/docs/modeling/building-blocks/users OpenFGA User Modeling
 */
interface UserInterface extends ModelInterface
{
    /**
     * Get the difference operation for this user.
     *
     * Difference operations enable sophisticated access control by subtracting one
     * set of users from another, creating complex user definitions that include some
     * users while explicitly excluding others. For example, "all editors except
     * contractors" or "all organization members except suspended users."
     *
     * This pattern is particularly useful for:
     * - Implementing exception-based access policies
     * - Temporary access restrictions without modifying base permissions
     * - Complex organizational hierarchies with exclusion rules
     * - Compliance scenarios requiring explicit user exclusions
     *
     * When a difference operation is present, the authorization system evaluates
     * both the base user set and the excluded user set, granting access only
     * to users who match the base set but not the exclusion set.
     *
     * @return DifferenceV1Interface|null The difference operation defining included and excluded user sets, or null if this is not a difference-based user
     */
    public function getDifference(): ?DifferenceV1Interface;

    /**
     * Get the user object representation.
     *
     * User objects represent direct, concrete user identifiers within the authorization
     * system. These can be structured objects with explicit type and ID properties,
     * or simple string identifiers following the "type:id" convention for backward
     * compatibility and convenience.
     *
     * Examples of user object representations:
     * - Structured: UserObject with type="user" and id="alice"
     * - String format: "user:alice," "service:backup-agent," "bot:notification-service"
     *
     * Direct user objects are the most straightforward way to assign permissions
     * to specific, known entities in your system. They provide clear, unambiguous
     * identification and are efficient for authorization queries.
     *
     * @return string|UserObjectInterface|null The direct user identifier as a structured object or string, or null if this is not a direct user reference
     */
    public function getObject(): null | UserObjectInterface | string;

    /**
     * Get the userset reference for this user.
     *
     * Usersets define dynamic user groups through relationships to other objects,
     * enabling permissions that automatically adapt as relationships change in your
     * system. A userset specifies users indirectly by describing a relationship
     * pattern, such as "all editors of document:readme" or "all members of group:engineering."
     *
     * Usersets are powerful because they:
     * - Automatically include/exclude users as relationships change
     * - Reduce the need for explicit permission management
     * - Enable permission inheritance and delegation patterns
     * - Support complex organizational structures and role hierarchies
     *
     * When authorization checks encounter usersets, OpenFGA recursively evaluates
     * the referenced relationships to determine the actual set of users that
     * have access through this indirect relationship.
     *
     * @return UsersetUserInterface|null The userset definition specifying users through relationships, or null if this is not a userset-based user
     */
    public function getUserset(): ?UsersetUserInterface;

    /**
     * Get the wildcard definition for this user.
     *
     * Wildcards represent all users of a specific type, enabling broad, type-based
     * permissions without enumerating individual users. This pattern is particularly
     * useful for organization-wide permissions, public access scenarios, or when
     * you want to grant access to all users matching certain criteria.
     *
     * Common wildcard use cases:
     * - "All employees can access the company directory"
     * - "All authenticated users can read public documents"
     * - "All service accounts can write to audit logs"
     * - "All users in the organization can view the org chart"
     *
     * Wildcards are efficient for authorization because they don't require
     * maintaining explicit relationships for every user, while still providing
     * type-safe access control based on user categorization.
     *
     * @return TypedWildcardInterface|null The wildcard definition specifying the user type, or null if this is not a wildcard user
     */
    public function getWildcard(): ?TypedWildcardInterface;

    /**
     * Serialize the user for JSON encoding.
     *
     * This method prepares the user data for API communication with the OpenFGA service,
     * converting the user representation into the format expected by the OpenFGA API.
     * The serialization handles all user types (direct objects, usersets, wildcards,
     * and difference operations) and ensures the resulting structure matches the
     * OpenFGA API specification.
     *
     * Only the appropriate user type fields are included in the output:
     * - Direct users include object field with type:id or structured object
     * - Usersets include userset field with type, id, and relation
     * - Wildcards include wildcard field with type specification
     * - Difference operations include difference field with base and subtract sets
     *
     * @return array<string, mixed> User data formatted for JSON encoding with the appropriate user type representation
     */
    #[Override]
    public function jsonSerialize(): array;
}
