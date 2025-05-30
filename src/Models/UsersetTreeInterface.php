<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use Override;

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
