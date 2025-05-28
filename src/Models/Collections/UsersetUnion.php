<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\{Userset, UsersetInterface};

use Override;

/**
 * @extends IndexedCollection<UsersetInterface>
 */
final class UsersetUnion extends IndexedCollection
{
    protected static string $itemType = Userset::class;

    /**
     * @inheritDoc
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return [
            'child' => parent::jsonSerialize(),
        ];
    }
}
