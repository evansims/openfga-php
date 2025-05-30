<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\{ModelInterface, TupleKey, TupleKeyInterface};
use Override;

/**
 * Collection implementation for OpenFGA tuple key objects.
 *
 * This class provides a concrete implementation for managing collections of
 * tuple key objects that define relationships between users, objects, and
 * relations in the OpenFGA authorization system. Tuple keys are the essential
 * components used to express authorization relationships and permissions.
 *
 * This collection is commonly used in write operations to specify which
 * relationships should be created or deleted in the authorization store.
 *
 * @extends IndexedCollection<TupleKeyInterface>
 *
 * @implements TupleKeysInterface<TupleKeyInterface>
 *
 * @see https://openfga.dev/docs/concepts#relationship-tuples OpenFGA Relationship Tuples
 */
final class TupleKeys extends IndexedCollection implements TupleKeysInterface
{
    /**
     * @phpstan-var class-string<TupleKeyInterface>
     *
     * @psalm-var class-string<ModelInterface>
     *
     * @var class-string<ModelInterface>
     */
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
