<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use Override;

interface UsersetUserInterface extends ModelInterface
{
    public function getId(): string;

    public function getRelation(): string;

    public function getType(): string;

    /**
     * @return array{type: string, id: string, relation: string}
     */
    #[Override]
    public function jsonSerialize(): array;
}
