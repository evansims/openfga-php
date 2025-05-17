<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * Represents a collection that is indexed by an integer, like a JSON array.
 *
 * @template T of ModelInterface
 *
 * @extends CollectionInterface<T>
 */
interface IndexedCollectionInterface extends CollectionInterface
{
}
