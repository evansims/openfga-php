<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use Override;

/**
 * Defines a difference operation node in authorization evaluation trees.
 *
 * UsersetTreeDifference represents a node in the userset evaluation tree that
 * computes the difference between two child nodes, effectively calculating
 * "users in base except those in subtract". This enables authorization patterns
 * where access is granted to one group while explicitly excluding another.
 *
 * Use this interface when working with authorization evaluation trees that
 * contain difference operations, typically returned from expand operations.
 */
interface UsersetTreeDifferenceInterface extends ModelInterface
{
    /**
     * Get the base node from which the subtract node will be removed.
     *
     * This represents the initial node in the userset tree from which users
     * will be subtracted to compute the final difference result.
     *
     * @return NodeInterface The base node for the difference operation
     */
    public function getBase(): NodeInterface;

    /**
     * Get the node representing users to subtract from the base.
     *
     * This represents the node in the userset tree whose users should be
     * removed from the base node to compute the final difference result.
     *
     * @return NodeInterface The node to subtract from the base
     */
    public function getSubtract(): NodeInterface;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function jsonSerialize(): array;
}
