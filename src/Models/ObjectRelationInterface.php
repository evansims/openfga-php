<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use Override;

/**
 * Represents an object-relation pair in OpenFGA authorization models.
 *
 * Object-relation pairs are fundamental components that specify a relationship
 * between a specific object and a relation type. They are commonly used in:
 *
 * - Tuple definitions to specify what relationship exists
 * - Userset references to point to related objects
 * - Permission lookups to identify target resources
 *
 * The pair consists of:
 * - Object: The target resource (for example, "document:readme", "folder:private")
 * - Relation: The type of relationship (for example, "viewer", "editor", "owner")
 *
 * Examples:
 * - {object: "document:readme", relation: "viewer"}
 * - {object: "folder:private", relation: "owner"}
 * - {relation: "member"} (object can be omitted in some contexts)
 *
 * @see https://openfga.dev/docs/concepts#relationship-tuples OpenFGA Relationship Tuples
 */
interface ObjectRelationInterface extends ModelInterface
{
    /**
     * Get the object identifier in an object-relation pair.
     *
     * The object represents the resource or entity being referenced,
     * typically formatted as "type:id" where type describes the kind of resource.
     *
     * @return ?string The object identifier, or null if not specified
     */
    public function getObject(): ?string;

    /**
     * Get the relation name that defines the type of relationship to the object.
     *
     * The relation describes what kind of permission or relationship exists.
     * Common examples include "owner", "viewer", "editor", "member".
     *
     * @return string The non-empty relation name
     */
    public function getRelation(): ?string;

    /**
     * @return array{object?: string, relation?: string}
     */
    #[Override]
    public function jsonSerialize(): array;
}
