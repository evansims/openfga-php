<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @psalm-type UsersetTreeTupleToUsersetShape = array{base: NodeShape, subtract: NodeShape}
 */
interface UsersetTreeTupleToUsersetInterface extends ModelInterface
{
    public function getBase(): NodeInterface;

    public function getSubtract(): NodeInterface;

    /**
     * @return UsersetTreeTupleToUsersetShape
     */
    public function jsonSerialize(): array;

    /**
     * @param UsersetTreeTupleToUsersetShape $data
     */
    public static function fromArray(array $data): self;
}
