<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use Override;

interface UsersetTreeInterface extends ModelInterface
{
    public function getRoot(): NodeInterface;

    /**
     * @return array{root: array{name: string, leaf?: array{users?: array<int, string>, computed?: array{userset: string}, tupleToUserset?: mixed}, difference?: mixed, intersection?: mixed, union?: mixed}}
     */
    #[Override]
    public function jsonSerialize(): array;
}
