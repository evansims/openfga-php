<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use JsonSerializable;

/**
 * @psalm-type AssertionShape = array{tuple_key: TupleKeyShape, expectation: bool, contextual_tuples?: TupleKeysShape, context?: array}
 */
interface AssertionInterface extends JsonSerializable, ModelInterface
{
    public function getContext(): ?array;

    public function getContextualTuples(): ?TupleKeysInterface;

    public function getExpectation(): bool;

    public function getTupleKey(): TupleKeyInterface;

    /**
     * @return AssertionShape
     */
    public function jsonSerialize(): array;

    /**
     * @param AssertionShape $data
     */
    public static function fromArray(array $data): self;
}
