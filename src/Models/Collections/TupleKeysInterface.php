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
    /**
     * @return array<int, array{user: string, relation: string, object: string, condition?: array<string, mixed>}>
     */
    #[Override]
    public function jsonSerialize(): array;
}
