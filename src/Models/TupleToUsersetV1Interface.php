<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use Override;

interface TupleToUsersetV1Interface extends ModelInterface
{
    public function getComputedUserset(): ObjectRelationInterface;

    public function getTupleset(): ObjectRelationInterface;

    /**
     * @return array{tupleset: array{object?: string, relation?: string}, computed_userset: array{object?: string, relation?: string}}
     */
    #[Override]
    public function jsonSerialize(): array;
}
