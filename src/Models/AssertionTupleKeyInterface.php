<?php

declare(strict_types=1);

namespace OpenFGA\Models;

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
    public function jsonSerialize(): array;
}
