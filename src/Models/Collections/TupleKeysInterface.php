<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\TupleKeyInterface;
use Override;

/**
 * @template T of TupleKeyInterface
 *
 * @extends IndexedCollectionInterface<T>
 */
interface TupleKeysInterface extends IndexedCollectionInterface
{
    #[Override]
    /**
     * @return array<int|string, mixed>
     */
    public function jsonSerialize(): array;
}
