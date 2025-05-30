<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Models\Collections\UsersListInterface;
use Override;

/**
 * Represents a leaf node in OpenFGA's userset tree structure.
 *
 * Leaf nodes are terminal nodes in the authorization evaluation tree that
 * define the actual users or user resolution methods. They represent the
 * final step in determining who has access to a particular resource through
 * a specific relation.
 *
 * A leaf can specify users through one of three mechanisms:
 *
 * 1. **Direct users**: An explicit list of user identifiers
 * 2. **Computed userset**: A reference to another userset to be evaluated
 * 3. **Tuple-to-userset**: A complex resolution that follows tuple relationships
 *
 * Only one of these mechanisms should be active in any given leaf node,
 * as they represent different strategies for determining the final user set.
 *
 * @see https://openfga.dev/docs/concepts#usersets OpenFGA Usersets
 * @see https://openfga.dev/docs/concepts#computed-userset Computed Usersets
 */
interface LeafInterface extends ModelInterface
{
    /**
     * Get the computed userset specification for this leaf.
     *
     * When present, this defines a computed relationship that resolves
     * to other usersets dynamically. This allows for indirect relationships
     * where users are determined by following other relations.
     *
     * @return ComputedInterface|null The computed userset specification, or null if not used
     */
    public function getComputed(): ?ComputedInterface;

    /**
     * Get the tuple-to-userset operation for this leaf.
     *
     * When present, this defines how to compute users by examining tuples
     * and resolving them to usersets. This enables complex relationship
     * patterns where users are derived from tuple relationships.
     *
     * @return UsersetTreeTupleToUsersetInterface|null The tuple-to-userset operation, or null if not used
     */
    public function getTupleToUserset(): ?UsersetTreeTupleToUsersetInterface;

    /**
     * Get the direct list of users for this leaf node.
     *
     * When present, this provides an explicit list of users who have
     * access through this leaf. This is used for direct user assignments
     * rather than computed or derived access patterns.
     *
     * @return UsersListInterface<UsersListUserInterface>|null The list of users with direct access, or null if not used
     */
    public function getUsers(): ?UsersListInterface;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function jsonSerialize(): array;
}
