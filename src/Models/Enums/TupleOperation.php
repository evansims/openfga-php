<?php

declare(strict_types=1);

namespace OpenFGA\Models\Enums;

use OpenFGA\{Messages, Translation\Translator};

/**
 * Operations that can be performed on relationship tuples in OpenFGA.
 *
 * This enum defines the available operations for managing relationship tuples through
 * the OpenFGA write API. Tuples represent the actual relationships between users,
 * objects, and relations that form the foundation of all authorization decisions.
 * These operations enable dynamic management of authorization data by adding and
 * removing relationships as your system evolves.
 *
 * Tuple operations are atomic and transactional, ensuring consistency in authorization
 * data. They can be batched together in write requests to perform multiple relationship
 * changes simultaneously, maintaining referential integrity across related permissions.
 *
 * These operations support:
 * - Dynamic permission assignment and revocation
 * - User lifecycle management (onboarding/offboarding)
 * - Role-based access control updates
 * - Temporary access grants and restrictions
 * - Organizational structure changes
 *
 * @see https://openfga.dev/docs/concepts#relationship-tuples OpenFGA Relationship Tuples
 * @see https://openfga.dev/docs/api/service#/Relationship%20Tuples/Write Write API Documentation
 */
enum TupleOperation: string
{
    /**
     * Delete operation for removing existing relationship tuples.
     *
     * This operation removes an existing relationship tuple from the authorization store,
     * effectively revoking the relationship between the specified user, object, and
     * relation. The deletion is immediate and will affect subsequent authorization
     * checks that depend on this relationship.
     *
     * Use cases for delete operations:
     * - Revoking user permissions when access should be removed
     * - User offboarding and access cleanup
     * - Removing temporary access grants that have expired
     * - Correcting incorrectly assigned permissions
     * - Organizational changes that require permission updates
     *
     * Delete operations are idempotent - attempting to delete a non-existent
     * tuple will not cause an error, making them safe for cleanup operations
     * and ensuring consistent behavior in distributed environments.
     */
    case TUPLE_OPERATION_DELETE = 'TUPLE_OPERATION_DELETE';

    /**
     * Write operation for adding new relationship tuples.
     *
     * This operation adds a new relationship tuple to the authorization store,
     * establishing a relationship between the specified user, object, and relation.
     * The new relationship becomes immediately available for authorization checks
     * and will be considered in all relevant permission evaluations.
     *
     * Use cases for write operations:
     * - Granting users new permissions on resources
     * - User onboarding and initial access setup
     * - Dynamic role assignments and promotions
     * - Sharing permissions and delegation scenarios
     * - Creating group memberships and organizational relationships
     *
     * Write operations will overwrite existing tuples with the same key,
     * ensuring that permission grants are idempotent and can be safely
     * repeated without creating duplicate relationships.
     */
    case TUPLE_OPERATION_WRITE = 'TUPLE_OPERATION_WRITE';

    /**
     * Get a user-friendly description of what this operation does.
     *
     * Provides a clear explanation of the operation's effect on authorization
     * data, useful for logging, auditing, and user interfaces.
     *
     * @return string A descriptive explanation of the operation
     */
    public function getDescription(): string
    {
        return match ($this) {
            self::TUPLE_OPERATION_WRITE => Translator::trans(Messages::TUPLE_OPERATION_WRITE_DESCRIPTION),
            self::TUPLE_OPERATION_DELETE => Translator::trans(Messages::TUPLE_OPERATION_DELETE_DESCRIPTION),
        };
    }

    /**
     * Check if this operation adds permissions to the authorization store.
     *
     * Useful for understanding whether an operation will grant new access
     * or capabilities to users within the system.
     *
     * @return bool True if the operation adds permissions, false otherwise
     */
    public function grantsPermissions(): bool
    {
        return match ($this) {
            self::TUPLE_OPERATION_WRITE => true,
            self::TUPLE_OPERATION_DELETE => false,
        };
    }

    /**
     * Check if this operation is safe to retry in case of failures.
     *
     * Idempotent operations can be safely retried without causing unintended
     * side effects, making them suitable for retry logic and distributed systems.
     *
     * @return true True if the operation is idempotent, false otherwise
     */
    public function isIdempotent(): bool
    {
        return true; // Both write and delete operations are idempotent in OpenFGA
    }

    /**
     * Check if this operation removes permissions from the authorization store.
     *
     * Useful for understanding whether an operation will revoke existing access
     * or capabilities from users within the system.
     *
     * @return bool True if the operation removes permissions, false otherwise
     */
    public function revokesPermissions(): bool
    {
        return match ($this) {
            self::TUPLE_OPERATION_DELETE => true,
            self::TUPLE_OPERATION_WRITE => false,
        };
    }
}
