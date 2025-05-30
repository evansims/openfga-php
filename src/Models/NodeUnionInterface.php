<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use Override;

/**
 * Represents a union operation between multiple nodes in a userset tree.
 *
 * A node union combines multiple authorization nodes where users from
 * any of the constituent nodes are included in the result set. This
 * implements the logical OR operation in authorization evaluation.
 */
interface NodeUnionInterface extends ModelInterface
{
    /**
     * Get the collection of nodes that participate in this union.
     *
     * Returns all the nodes that are combined in this union operation.
     * The union result includes users from any of these nodes.
     *
     * @return array<int, NodeInterface> The array of nodes in the union
     */
    public function getNodes(): array;

    /**
     * Serialize the node union to its JSON representation.
     *
     * @return array{nodes: array<int, mixed>} The serialized node union data
     */
    #[Override]
    public function jsonSerialize(): array;
}
