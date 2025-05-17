<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @psalm-type TypedWildcardShape = array{type: string}
 */
interface TypedWildcardInterface extends ModelInterface
{
    public function __toString(): string;

    public function getType(): string;

    /**
     * @return TypedWildcardShape
     */
    public function jsonSerialize(): array;
}
