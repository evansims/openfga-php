<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use Override;

interface UserObjectInterface extends ModelInterface
{
    public function __toString(): string;

    public function getId(): string;

    public function getType(): string;

    /**
     * @return array{type: string, id: string}
     */
    #[Override]
    public function jsonSerialize(): array;
}
