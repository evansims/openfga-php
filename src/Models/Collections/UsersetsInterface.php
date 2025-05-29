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
interface UsersetsInterface extends IndexedCollectionInterface
{
    /**
     * @return array{child: array<mixed>}
     */
    #[Override]
    public function jsonSerialize(): array;
}
