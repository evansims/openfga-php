<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use JsonSerializable;

interface AssertionInterface extends ModelInterface, JsonSerializable
{
    public function getContext(): ?array;

    public function getContextualTuples(): ?TupleKeysInterface;

    public function getExpectation(): bool;

    public function getTupleKey(): AssertionTupleKeyInterface;

    public function jsonSerialize(): array;

    public static function fromArray(array $data): self;
}
