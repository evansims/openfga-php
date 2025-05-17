<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @extends AbstractIndexedCollection<Tuple>
 */
final class Tuples extends AbstractIndexedCollection implements TuplesInterface
{
    protected static string $itemType = Tuple::class;
}
