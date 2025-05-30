<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use Override;

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

    #[Override]
    public function jsonSerialize(): string;
}
