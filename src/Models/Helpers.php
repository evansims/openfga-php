<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Models\Collections\TupleKeys;

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
 * @param TupleKey ...$tuples
 */
function tuples(TupleKey ...$tuples): TupleKeys
{
    return new TupleKeys($tuples);
}
