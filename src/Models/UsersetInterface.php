<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Models\Collections\UsersetsInterface;
use Override;

/**
 * Defines the contract for userset specifications in authorization models.
 *
 * A userset represents a collection of users that can be computed through
 * various means: direct assignment, computed relationships, unions, intersections,
 * or complex tuple-to-userset operations. This interface provides the foundation
 * for all userset types used in OpenFGA authorization models.
 *
 * Use this when defining how groups of users are identified and computed
 * in your authorization system.
 */
interface UsersetInterface extends ModelInterface
{
    /**
     * Get the computed userset specification for this userset.
     *
     * A computed userset defines relationships that are derived from other relationships,
     * allowing for indirect authorization patterns. When present, this specifies
     * an object-relation pair that should be computed to determine the actual users.
     *
     * @return ObjectRelationInterface|null The computed userset specification, or null if not used
     */
    public function getComputedUserset(): ?ObjectRelationInterface;

    /**
     * Get the difference operation specification for this userset.
     *
     * A difference operation represents a set subtraction where users are granted
     * access based on one userset but explicitly excluded if they're in another.
     * This enables sophisticated access control patterns like "all managers except those on leave".
     *
     * @return DifferenceV1Interface|null The difference operation specification, or null if not used
     */
    public function getDifference(): ?DifferenceV1Interface;

    /**
     * Get the direct userset value for this userset.
     *
     * A direct userset represents an immediate, explicit relationship without
     * complex computation. This is typically used for simple membership patterns
     * where users are directly assigned to a role or permission.
     *
     * @return object|null The direct userset value, or null if not used
     */
    public function getDirect(): ?object;

    /**
     * Get the intersection operation specification for this userset.
     *
     * An intersection operation represents users who must satisfy ALL of the
     * specified usersets. This creates a logical AND operation where users
     * are granted access only if they're in every userset within the intersection.
     *
     * @return UsersetsInterface|null The collection of usersets to intersect, or null if not used
     */
    public function getIntersection(): ?UsersetsInterface;

    /**
     * Get the tuple-to-userset operation specification for this userset.
     *
     * A tuple-to-userset operation computes users by examining existing relationships
     * and following them to other usersets. This enables complex authorization patterns
     * where permissions are inherited through relationship chains.
     *
     * @return TupleToUsersetV1Interface|null The tuple-to-userset operation specification, or null if not used
     */
    public function getTupleToUserset(): ?TupleToUsersetV1Interface;

    /**
     * Get the union operation specification for this userset.
     *
     * A union operation represents users who satisfy ANY of the specified
     * usersets. This creates a logical OR operation where users are granted
     * access if they're in at least one userset within the union.
     *
     * @return UsersetsInterface|null The collection of usersets to unite, or null if not used
     */
    public function getUnion(): ?UsersetsInterface;

    /**
     * @return array{
     *     computedUserset?: array{object?: string, relation?: string},
     *     tupleToUserset?: array{tupleset: array{object?: string, relation?: string}, computedUserset: array{object?: string, relation?: string}},
     *     union?: array<mixed>,
     *     intersection?: array<mixed>,
     *     difference?: array{base: array<mixed>, subtract: array<mixed>},
     *     this?: object,
     * }
     */
    #[Override]
    public function jsonSerialize(): array;
}
