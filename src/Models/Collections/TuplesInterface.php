<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\TupleInterface;
use Override;

/**
 * Collection interface for OpenFGA tuple objects.
 *
 * This interface defines a collection that holds tuple objects representing
 * relationship facts in the OpenFGA authorization system. Tuples define
 * the actual relationships between users, objects, and relations that are
 * used for authorization decisions.
 *
 * Each tuple contains a key (defining the relationship) and a timestamp
 * (recording when the relationship was established), making them essential
 * for both authorization checks and audit trails.
 *
 * @template T of TupleInterface
 *
 * @extends IndexedCollectionInterface<T>
 *
 * @see https://openfga.dev/docs/concepts#relationship-tuples OpenFGA Relationship Tuples
 */
interface TuplesInterface extends IndexedCollectionInterface
{
    /**
     * @return array<int, array{key: array{user: string, relation: string, object: string, condition?: array<string, mixed>}, timestamp: string}>
     */
    #[Override]
    public function jsonSerialize(): array;
}
