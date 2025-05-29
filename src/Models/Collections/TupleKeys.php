<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\{TupleKey, TupleKeyInterface};
use Override;

/**
 * @extends IndexedCollection<TupleKeyInterface>
 *
 * @implements TupleKeysInterface<TupleKeyInterface>
 */
final class TupleKeys extends IndexedCollection implements TupleKeysInterface
{
    protected static string $itemType = TupleKey::class;

    /**
     * @inheritDoc
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return [
            'tuple_keys' => parent::jsonSerialize(),
        ];
    }
}
