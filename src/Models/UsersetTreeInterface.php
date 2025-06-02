<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use Override;

/**
 * Defines a tree structure for representing complex userset operations.
 *
 * UsersetTree provides a hierarchical representation of authorization
 * evaluation logic, where each node can contain unions, intersections,
 * differences, computed usersets, or tuple-to-userset operations.
 * This tree structure enables OpenFGA to represent and evaluate
 * sophisticated authorization patterns efficiently.
 *
 * Use this interface when working with authorization evaluation trees
 * returned by expand operations or when implementing custom authorization
 * logic that needs to traverse userset structures.
 */
interface UsersetTreeInterface extends ModelInterface
{
    /**
     * Get the root node of the userset tree structure.
     *
     * This returns the top-level node that represents the entry point for
     * userset expansion. The tree structure allows for complex authorization
     * logic including unions, intersections, and difference operations.
     *
     * @return NodeInterface The root node of the userset tree
     */
    public function getRoot(): NodeInterface;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function jsonSerialize(): array;
}
