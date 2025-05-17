<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @psalm-type AssertionTupleKeyShape = array{user: string, relation: string, object: string}
 */
interface AssertionTupleKeyInterface extends ModelInterface
{
    public function getObject(): string;

    public function getRelation(): string;

    public function getUser(): string;

    /**
     * @return AssertionTupleKeyShape
     */
    public function jsonSerialize(): array;
}
