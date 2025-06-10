<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use Override;

/**
 * Represents a collection of users in authorization contexts.
 *
 * This collection manages users who have been granted access through various
 * authorization mechanisms. Users can be individual identities, usersets,
 * or wildcard patterns depending on the authorization model configuration.
 *
 * @extends IndexedCollectionInterface<\OpenFGA\Models\UserInterface>
 */
interface UsersInterface extends IndexedCollectionInterface
{
    /**
     * @return array<int, array{object?: mixed, userset?: array{type: string, id: string, relation: string}, wildcard?: array{type: string}}>
     */
    #[Override]
    public function jsonSerialize(): array;
}
