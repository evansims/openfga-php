<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\Collections\UsersInterface;
use OpenFGA\Models\UserInterface;
use OpenFGA\Schema\SchemaInterface;

/**
 * Interface for user listing response objects.
 *
 * This interface defines the contract for responses returned when listing users
 * that have a specific relationship with an object in OpenFGA. This is the inverse
 * of permission checking - instead of asking "can this user access this object?",
 * it asks "which users can access this object?".
 *
 * User listing is particularly useful for building administrative interfaces,
 * access reports, and user management features that need to display who has
 * access to specific resources.
 *
 * @see UsersInterface Collection of user objects
 * @see https://openfga.dev/docs/interacting/relationship-queries OpenFGA List Users API Documentation
 */
interface ListUsersResponseInterface extends ResponseInterface
{
    /**
     * Get the schema definition for this response.
     *
     * Returns the schema that defines the structure and validation rules for user
     * listing response data, ensuring consistent parsing and validation of API responses.
     *
     * @return SchemaInterface The schema definition for response validation
     */
    public static function schema(): SchemaInterface;

    /**
     * Get the collection of users with the specified relationship.
     *
     * Returns a type-safe collection containing the user objects that have the
     * queried relationship with the specified object. Each user represents an
     * entity that has been granted the specified permission or relationship.
     *
     * @return UsersInterface<UserInterface> The collection of users with the relationship
     */
    public function getUsers(): UsersInterface;
}
