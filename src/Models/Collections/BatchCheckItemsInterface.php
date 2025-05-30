<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\BatchCheckItemInterface;

/**
 * Collection of batch check items for batch authorization requests.
 *
 * This collection maintains a list of BatchCheckItem objects, each representing
 * a single authorization check to be performed as part of a batch request.
 *
 * @extends IndexedCollectionInterface<BatchCheckItemInterface>
 *
 * @see BatchCheckItemInterface
 * @see https://openfga.dev/docs/api#/Relationship%20Queries/BatchCheck
 */
interface BatchCheckItemsInterface extends IndexedCollectionInterface
{
}
