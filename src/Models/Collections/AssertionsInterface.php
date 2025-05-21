<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\AssertionInterface;
use Override;

/**
 * @template T of AssertionInterface
 *
 * @extends IndexedCollectionInterface<T>
 */
interface AssertionsInterface extends IndexedCollectionInterface
{
    /**
     * @return array<int, array{
     *     tuple_key: array<string, mixed>,
     *     expectation: bool,
     *     contextual_tuples?: array<array-key, mixed>,
     *     context?: array<array-key, mixed>
     * }>
     */
    #[Override]
    public function jsonSerialize(): array;
}
