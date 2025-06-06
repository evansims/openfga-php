<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\ComputedInterface;
use Override;

/**
 * @extends IndexedCollectionInterface<ComputedInterface>
 */
interface ComputedsInterface extends IndexedCollectionInterface
{
    /**
     * @return array<int, array{userset: string}>
     */
    #[Override]
    public function jsonSerialize(): array;
}
