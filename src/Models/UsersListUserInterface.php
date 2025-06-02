<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use Override;

/**
 * Represents a user in a list context for authorization operations.
 *
 * UsersListUser provides a simple wrapper around user identifiers, ensuring
 * they conform to the expected format and can be properly serialized for
 * API operations. This is commonly used in list operations where user
 * identifiers need to be processed in bulk.
 *
 * Use this interface when working with lists of users in authorization
 * contexts, such as batch operations or user enumeration.
 */
interface UsersListUserInterface extends ModelInterface
{
    /**
     * Return the string representation of the user.
     *
     * This provides a convenient way to get the user identifier as a string,
     * typically used for display purposes or when the user needs to be
     * converted to a string format.
     *
     * @return string The string representation of the user
     */
    public function __toString(): string;

    /**
     * Get the user identifier string.
     *
     * This returns the user identifier in the format expected by OpenFGA,
     * typically "type:id" where type describes the kind of user and id
     * is the unique identifier for that user.
     *
     * @return string The user identifier string
     */
    public function getUser(): string;

    /**
     * Serialize the user to its JSON representation.
     *
     * Returns the user identifier as a string for API serialization.
     * This differs from most models which serialize to arrays.
     *
     * @return string The user identifier string
     */
    #[Override]
    public function jsonSerialize(): string;
}
