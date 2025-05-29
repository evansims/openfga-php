<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\UsersetInterface;
use Override;

/**
 * @template T of UsersetInterface
 *
 * @extends IndexedCollectionInterface<T>
 */
interface UsersetUnionInterface extends IndexedCollectionInterface
{
    /**
     * @return array<int, array{
     *     child: array<string, mixed>,
     * }>
     */
    #[Override]
    public function jsonSerialize(): array;
}
