<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use Override;

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
