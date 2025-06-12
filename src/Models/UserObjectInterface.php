<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use Override;

/**
 * Represents a user object in OpenFGA authorization model.
 *
 * User objects are typed entities that can be subjects in authorization
 * relationships. They consist of a type (for example 'user', 'group') and
 * a unique identifier within that type.
 */
interface UserObjectInterface extends ModelInterface
{
    /**
     * Get the string representation of the user object.
     *
     * Returns the user object in the format 'type:id' which is the
     * standard OpenFGA format for representing typed objects.
     *
     * @return string The user object as 'type:id'
     */
    public function __toString(): string;

    /**
     * Get the unique identifier of the user object.
     *
     * The ID is unique within the context of the object type and
     * represents the specific instance of the typed object.
     *
     * @return string The object identifier
     */
    public function getId(): string;

    /**
     * Get the type of the user object.
     *
     * The type defines the category or class of the object (for example 'user',
     * 'group', 'organization') and must be defined in the authorization model.
     *
     * @return string The object type
     */
    public function getType(): string;

    /**
     * Serialize the user object to its JSON representation.
     *
     * @return array{type: string, id: string} The serialized user object data
     */
    #[Override]
    public function jsonSerialize(): array;
}
