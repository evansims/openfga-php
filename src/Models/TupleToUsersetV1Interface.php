<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use Override;

interface TupleToUsersetV1Interface extends ModelInterface
{
    /**
     * Get the userset that will be computed based on the tupleset.
     *
     * This represents the object-relation pair that defines which userset should be
     * computed for each tuple found in the tupleset. The computed userset determines
     * the final set of users resulting from the tuple-to-userset operation.
     *
     * @return ObjectRelationInterface The object-relation pair defining the computed userset
     */
    public function getComputedUserset(): ObjectRelationInterface;

    /**
     * Get the tupleset (object-relation pair) that defines which tuples to use for computation.
     *
     * This represents the object-relation pair that identifies which tuples should be
     * examined to compute the final userset. For each matching tuple, the computed
     * userset will be evaluated to determine the resulting users.
     *
     * @return ObjectRelationInterface The object-relation pair defining the tupleset
     */
    public function getTupleset(): ObjectRelationInterface;

    /**
     * @return array{tupleset: array{object?: string, relation?: string}, computedUserset: array{object?: string, relation?: string}}
     */
    #[Override]
    public function jsonSerialize(): array;
}
