<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\ComputedInterface;
use Override;

/**
 * @template T of ComputedInterface
 *
 * @extends IndexedCollectionInterface<T>
 */
interface ComputedsInterface extends IndexedCollectionInterface
{
    /**
     * @return array<int, array{userset: string}>
     */
    #[Override]
    public function jsonSerialize(): array;
}
