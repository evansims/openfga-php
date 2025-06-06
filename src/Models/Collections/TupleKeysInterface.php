<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use Override;

/**
 * A collection of relationship tuple keys.
 *
 * This interface represents a type-safe collection of tuple keys that define relationships
 * between users, objects, and relations in the authorization model. Tuple keys are the
 * fundamental building blocks for expressing permissions and relationships.
 *
 * @extends IndexedCollectionInterface<\OpenFGA\Models\TupleKeyInterface>
 *
 * @see https://openfga.dev/docs/concepts#relationship-tuples OpenFGA Relationship Tuples
 */
interface TupleKeysInterface extends IndexedCollectionInterface
{
    /**
     * Serialize the tuple keys collection for JSON encoding.
     *
     * This method prepares the collection of tuple keys for JSON serialization, ensuring
     * that each tuple key is properly formatted for API requests or storage.
     *
     * @return array<int|string, mixed> The serialized collection ready for JSON encoding
     */
    #[Override]
    public function jsonSerialize(): array;
}
