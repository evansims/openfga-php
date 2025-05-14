<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @psalm-type TupleToUsersetShape = array{tupleset: ObjectRelationShape, computed_userset: ObjectRelationShape}
 */
interface TupleToUsersetV1Interface extends ModelInterface
{
    public function getComputedUserset(): ObjectRelationInterface;

    public function getTupleset(): ObjectRelationInterface;

    /**
     * @return TupleToUsersetShape
     */
    public function jsonSerialize(): array;

    /**
     * @param TupleToUsersetShape $data
     */
    public static function fromArray(array $data): self;
}
