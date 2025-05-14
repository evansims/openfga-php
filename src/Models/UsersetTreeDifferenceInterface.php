<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @psalm-type UsersetTreeDifferenceShape = array{base: NodeShape, subtract: NodeShape}
 */
interface UsersetTreeDifferenceInterface extends ModelInterface
{
    public function getBase(): NodeInterface;

    public function getSubtract(): NodeInterface;

    /**
     * @return UsersetTreeDifferenceShape
     */
    public function jsonSerialize(): array;

    /**
     * @param UsersetTreeDifferenceShape $data
     */
    public static function fromArray(array $data): self;
}
