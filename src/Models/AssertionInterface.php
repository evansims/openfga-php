<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Models\Collections\TupleKeysInterface;

interface AssertionInterface extends ModelInterface
{
    /**
     * @return null|array<string, mixed>
     */
    public function getContext(): ?array;

    /**
     * @return null|TupleKeysInterface<TupleKeyInterface>
     */
    public function getContextualTuples(): ?TupleKeysInterface;

    public function getExpectation(): bool;

    public function getTupleKey(): AssertionTupleKeyInterface;

    /**
     * @return array{
     *     tuple_key: array<string, mixed>,
     *     expectation: bool,
     *     contextual_tuples?: array<array-key, mixed>,
     *     context?: array<array-key, mixed>
     * }
     */
    public function jsonSerialize(): array;
}
