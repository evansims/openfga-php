<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * Represents a collection that is indexed by a string, like a JSON object.
 *
 * @template TKey of string
 *
 * @extends CollectionInterface<TKey>
 */
interface KeyedCollectionInterface extends CollectionInterface
{
}
