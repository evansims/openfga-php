<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface TypedWildcardInterface extends ModelInterface
{
    public function __toString(): string;

    public function getType(): string;

    /**
     * @return array{type: string}
     */
    public function jsonSerialize(): array;
}
