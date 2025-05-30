<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use Override;

/**
 * Represents a computed userset in OpenFGA authorization models.
 *
 * Computed usersets allow you to define relationships that are calculated
 * dynamically based on other relationships. Instead of storing direct
 * relationships, computed usersets reference other relations that should
 * be evaluated to determine the effective permissions.
 *
 * For example, if you want "viewers" of a document to include everyone
 * who is an "editor" of that document, you could use a computed userset
 * that references the "editor" relation.
 *
 * Common userset reference formats:
 * - "#relation" - References a relation on the same object
 * - "object#relation" - References a relation on a specific object
 *
 * @see https://openfga.dev/docs/modeling/direct-access OpenFGA Direct Access
 * @see https://openfga.dev/docs/concepts#computed-userset OpenFGA Computed Usersets
 */
interface ComputedInterface extends ModelInterface
{
    /**
     * Get the userset reference string that defines a computed relationship.
     *
     * This represents a reference to another userset that should be computed dynamically
     * based on relationships. The userset string typically follows the format "#relation"
     * to reference a relation on the same object type.
     *
     * @return string The userset reference string defining the computed relationship
     */
    public function getUserset(): string;

    /**
     * @return array{userset: string}
     */
    #[Override]
    public function jsonSerialize(): array;
}
