<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\UserInterface;

/**
 * @template T of UserInterface
 *
 * @extends IndexedCollectionInterface<T>
 */
interface UsersInterface extends IndexedCollectionInterface
{
    /**
     * @return array<int, array{object?: mixed, userset?: array{type: string, id: string, relation: string}, wildcard?: array{type: string}}>
     */
    public function jsonSerialize(): array;
}
