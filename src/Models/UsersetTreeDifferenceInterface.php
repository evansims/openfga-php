<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use Override;

interface UsersetTreeDifferenceInterface extends ModelInterface
{
    public function getBase(): NodeInterface;

    public function getSubtract(): NodeInterface;

    /**
     * @return array{base: array{name: string, leaf?: array{users?: array<int, string>, computed?: array{userset: string}, tupleToUserset?: mixed}, difference?: mixed, intersection?: mixed, union?: mixed}, subtract: array{name: string, leaf?: array{users?: array<int, string>, computed?: array{userset: string}, tupleToUserset?: mixed}, difference?: mixed, intersection?: mixed, union?: mixed}}
     */
    #[Override]
    public function jsonSerialize(): array;
}
