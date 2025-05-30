<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\{Computed, ComputedInterface, ModelInterface};

/**
 * @extends IndexedCollection<ComputedInterface>
 *
 * @implements ComputedsInterface<ComputedInterface>
 */
final class Computeds extends IndexedCollection implements ComputedsInterface
{
    /**
     * @phpstan-var class-string<ComputedInterface>
     *
     * @psalm-var class-string<ModelInterface>
     *
     * @var class-string<ModelInterface>
     */
    protected static string $itemType = Computed::class;
}
