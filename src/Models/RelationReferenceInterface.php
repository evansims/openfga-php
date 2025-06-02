<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use Override;

/**
 * Defines the contract for relation references with optional conditions.
 *
 * A relation reference specifies a particular relation within a type definition,
 * optionally with an associated condition that must be satisfied. This allows
 * for conditional access patterns where relationships are only valid when
 * certain runtime conditions are met.
 *
 * Use this when you need to reference specific relations in your authorization
 * model, especially when implementing attribute-based access control (ABAC) patterns.
 */
interface RelationReferenceInterface extends ModelInterface
{
    /**
     * Get the optional condition name that must be satisfied.
     *
     * When specified, this condition must evaluate to true for the
     * relation reference to be valid. This enables conditional access
     * based on runtime context and attributes.
     *
     * @return string|null The condition name, or null if no condition is required
     */
    public function getCondition(): ?string;

    /**
     * Get the optional specific relation on the referenced type.
     *
     * When specified, this limits the reference to a specific relation
     * on the target type rather than the entire type. This allows for
     * more precise relationship definitions.
     *
     * @return string|null The relation name, or null to reference the entire type
     */
    public function getRelation(): ?string;

    /**
     * Get the type being referenced.
     *
     * This is the object type that this reference points to. It defines
     * which type of objects can be used in relationships through this reference.
     *
     * @return string The type name being referenced
     */
    public function getType(): string;

    /**
     * Get the optional wildcard marker for type-level permissions.
     *
     * When present, this indicates that the reference applies to all
     * instances of the specified type, rather than specific instances.
     * This is useful for granting permissions at the type level.
     *
     * @return object|null The wildcard marker, or null for instance-specific references
     */
    public function getWildcard(): ?object;

    /**
     * @return array{type: string, relation?: string, wildcard?: object, condition?: string}
     */
    #[Override]
    public function jsonSerialize(): array;
}
