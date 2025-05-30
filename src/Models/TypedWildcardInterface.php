<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use Override;

interface TypedWildcardInterface extends ModelInterface
{
    /**
     * Return the string representation of the wildcard.
     *
     * This typically returns the wildcard in "type:*" format, representing
     * all users of the specified type. This is useful for granting permissions
     * to all entities of a particular type without listing them individually.
     *
     * @return string The string representation of the typed wildcard
     */
    public function __toString(): string;

    /**
     * Get the object type that this wildcard represents.
     *
     * This returns the type name for which the wildcard grants access to all
     * users of that type. For example, "user" would represent all users,
     * "group" would represent all groups, etc.
     *
     * @return string The object type that this wildcard represents
     */
    public function getType(): string;

    /**
     * @return array{type: string}
     */
    #[Override]
    public function jsonSerialize(): array;
}
