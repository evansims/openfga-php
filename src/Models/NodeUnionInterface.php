<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use Override;

interface NodeUnionInterface extends ModelInterface
{
    /**
     * @return array<int, NodeInterface>
     */
    public function getNodes(): array;

    /**
     * @return array{nodes: array<int, mixed>}
     */
    #[Override]
    public function jsonSerialize(): array;
}
