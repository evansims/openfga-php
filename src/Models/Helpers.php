<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Models\Collections\TupleKeys;

use function is_array;

/**
 * Helper for creating a TupleKey.

 *
 * @param string                  $type
 * @param string                  $relation
 * @param string                  $object
 * @param null|ConditionInterface $condition
 *
 * @return TupleKey
 */
function tuple(string $type, string $relation, string $object, ?ConditionInterface $condition = null): TupleKey
{
    return new TupleKey($type, $relation, $object, $condition);
}

/**
 * Helper for creating a TupleKeys collection.

 *
 * @param array<TupleKey>|TupleKey $tuples
 *
 * @return TupleKeys
 */
function tuples(TupleKey | array $tuples): TupleKeys
{
    if (! is_array($tuples)) {
        return new TupleKeys([$tuples]);
    }

    return new TupleKeys($tuples);
}
