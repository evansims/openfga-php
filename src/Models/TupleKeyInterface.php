<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface TupleKeyInterface extends ModelInterface
{
    public function getCondition(): ?ConditionInterface;

    public function getObject(): ?string;

    public function getRelation(): ?string;

    public function getUser(): ?string;

    /**
     * @return array{user?: string, relation?: string, object?: string, condition?: array{name: string, expression: string, parameters?: array{module: string, source_info: array{file: string}}, metadata?: array{module: string, source_info: array{file: string}}}}
     */
    public function jsonSerialize(): array;

    /**
     * @param array{user?: string, relation?: string, object?: string, condition?: array{name: string, expression: string, parameters?: array{module: string, source_info: array{file: string}}, metadata?: array{module: string, source_info: array{file: string}}}} $data
     */
    public static function fromArray(TupleKeyType $type, array $data): static;
}
