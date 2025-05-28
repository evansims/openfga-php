<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Models\Collections\TupleKeys;

use function is_array;

/**
 * Helper for creating a TupleKey.
 *
 * @param string                  $user
 * @param string                  $relation
 * @param string                  $object
 * @param null|ConditionInterface $condition
 */
function tuple(string $user, string $relation, string $object, ?ConditionInterface $condition = null): TupleKey
{
    return new TupleKey($user, $relation, $object, $condition);
}

/**
 * Helper for creating a TupleKeys collection.
 *
 * @param array<TupleKey>|TupleKey $tuples
 */
function tuples(TupleKey | array $tuples): TupleKeys
{
    if (! is_array($tuples)) {
        return new TupleKeys([$tuples]);
    }

    return new TupleKeys($tuples);
}
