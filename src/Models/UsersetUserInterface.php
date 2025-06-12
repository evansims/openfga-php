<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use Override;

/**
 * Defines the contract for userset user specifications.
 *
 * A userset user represents a reference to users through a userset relationship,
 * typically in the format "object#relation" where object is the entity and
 * relation defines which users are included. This allows dynamic user groups
 * based on relationships rather than static user lists.
 *
 * Use this when you need to reference users through relationship-based groups
 * in your authorization model.
 */
interface UsersetUserInterface extends ModelInterface
{
    /**
     * Get the object identifier in the userset reference.
     *
     * This represents the specific object instance that the userset refers to.
     * For example, in "group:eng#member," this would return "eng."
     *
     * @return string The object identifier
     */
    public function getId(): string;

    /**
     * Get the relation name in the userset reference.
     *
     * This represents the specific relation on the referenced object that
     * defines the userset. For example, in "group:eng#member," this would return "member."
     *
     * @return string The relation name
     */
    public function getRelation(): string;

    /**
     * Get the object type in the userset reference.
     *
     * This represents the type of object that the userset refers to.
     * For example, in "group:eng#member," this would return "group."
     *
     * @return string The object type
     */
    public function getType(): string;

    /**
     * @return array{type: string, id: string, relation: string}
     */
    #[Override]
    public function jsonSerialize(): array;
}
