<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface TupleToUsersetV1Interface extends ModelInterface
{
    public function getComputedUserset(): ObjectRelationInterface;

    public function getTupleset(): ObjectRelationInterface;

    /**
     * @return array{tupleset: array{object?: string, relation?: string}, computed_userset: array{object?: string, relation?: string}}
     */
    public function jsonSerialize(): array;
}
