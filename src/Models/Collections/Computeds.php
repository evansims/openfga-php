<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\{Computed, ComputedInterface};

/**
 * @extends IndexedCollection<ComputedInterface>
 *
 * @implements ComputedsInterface<ComputedInterface>
 */
final class Computeds extends IndexedCollection implements ComputedsInterface
{
    protected static string $itemType = Computed::class;
}
