<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @psalm-type UsersetTreeShape = array{root: NodeShape}
 */
interface UsersetTreeInterface extends ModelInterface
{
    public function getRoot(): NodeInterface;

    /**
     * @return UsersetTreeShape
     */
    public function jsonSerialize(): array;

    /**
     * @param UsersetTreeShape $data
     */
    public static function fromArray(array $data): static;
}
