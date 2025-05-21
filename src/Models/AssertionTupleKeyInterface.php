<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use Override;

interface AssertionTupleKeyInterface extends ModelInterface
{
    public function getObject(): string;

    public function getRelation(): string;

    public function getUser(): string;

    /**
     * @return array{
     *     user: string,
     *     relation: string,
     *     object: string,
     * }
     */
    #[Override]
    public function jsonSerialize(): array;
}
