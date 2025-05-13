<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use JsonSerializable;

interface AssertionInterface extends ModelInterface, JsonSerializable
{
    public function getContext(): ?array;

    public function getContextualTuples(): ?TupleKeysInterface;

    public function getExpectation(): bool;

    public function getTupleKey(): TupleKeyInterface;

    /**
     * @return array{tuple_key: array{user: string, relation: string, object: string, condition?: array{name: string, expression: string, parameters?: array{module: string, source_info: array{file: string}}, metadata?: array{module: string, source_info: array{file: string}}}}, expectation: bool, contextual_tuples?: array<int, array{user: string, relation: string, object: string, condition?: array{name: string, expression: string, parameters?: array{module: string, source_info: array{file: string}}, metadata?: array{module: string, source_info: array{file: string}}}}, context?: array}
     */
    public function jsonSerialize(): array;

    /**
     * @param array{tuple_key: array{user: string, relation: string, object: string, condition?: array{name: string, expression: string, parameters?: array{module: string, source_info: array{file: string}}, metadata?: array{module: string, source_info: array{file: string}}}}, expectation: bool, contextual_tuples?: array<int, array{user: string, relation: string, object: string, condition?: array{name: string, expression: string, parameters?: array{module: string, source_info: array{file: string}}, metadata?: array{module: string, source_info: array{file: string}}}}, context?: array} $data
     */
    public static function fromArray(array $data): self;
}
