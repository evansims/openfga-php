<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\AssertionInterface;
use Override;

/**
 * Collection interface for OpenFGA assertion models.
 *
 * This interface defines a collection that holds assertion objects used for
 * testing authorization model correctness. Assertions represent expected
 * authorization outcomes for specific tuple configurations.
 *
 * @template T of AssertionInterface
 *
 * @extends IndexedCollectionInterface<T>
 *
 * @see https://openfga.dev/docs/modeling/testing OpenFGA Model Testing
 */
interface AssertionsInterface extends IndexedCollectionInterface
{
    /**
     * @return array<int, array{
     *     tuple_key: array{user: string, relation: string, object: string},
     *     expectation: bool,
     *     contextual_tuples?: array<array-key, mixed>,
     *     context?: array<array-key, mixed>
     * }>
     */
    #[Override]
    public function jsonSerialize(): array;
}
