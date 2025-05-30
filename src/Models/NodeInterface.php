<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use Override;

/**
 * Represents a node in a userset tree structure.
 *
 * Nodes are fundamental building blocks in OpenFGA's authorization
 * model that represent different types of relationships and operations
 * within the access control evaluation tree.
 */
interface NodeInterface extends ModelInterface
{
    /**
     * Get the difference operation for this node.
     *
     * The difference operation represents a set subtraction where users
     * from one set are excluded from another set.
     *
     * @return ?UsersetTreeDifferenceInterface The difference operation or null if not applicable
     */
    public function getDifference(): ?UsersetTreeDifferenceInterface;

    /**
     * Get the intersection operation for this node.
     *
     * The intersection operation represents the common elements between
     * multiple usersets in the authorization tree.
     *
     * @return NodeUnionInterface|self|null The intersection node or null if not applicable
     */
    public function getIntersection(): null | self | NodeUnionInterface;

    /**
     * Get the leaf node if this is a terminal node.
     *
     * Leaf nodes represent the actual users, computed usersets, or
     * tuple-to-userset relationships at the end of the evaluation tree.
     *
     * @return ?LeafInterface The leaf node or null if this is not a leaf
     */
    public function getLeaf(): ?LeafInterface;

    /**
     * Get the name identifier for this node.
     *
     * The name is used to identify the node within the authorization
     * model and corresponds to relation names or other identifiers.
     *
     * @return string The node name
     */
    public function getName(): string;

    /**
     * Get the union operation for this node.
     *
     * The union operation represents the combination of multiple usersets
     * where users from any of the sets are included in the result.
     *
     * @return NodeUnionInterface|self|null The union node or null if not applicable
     */
    public function getUnion(): null | self | NodeUnionInterface;

    /**
     * Serialize the node to its JSON representation.
     *
     * @return array<string, mixed> The serialized node data
     */
    #[Override]
    public function jsonSerialize(): array;
}
