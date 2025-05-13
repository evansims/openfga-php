<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use JsonSerializable;

interface ConditionParametersInterface extends ModelCollectionInterface, JsonSerializable
{
    /**
     * Add a condition to the collection.
     *
     * @param ConditionParameterInterface $condition
     */
    public function add(ConditionParameterInterface $condition): void;

    /**
     * Get the current condition in the collection.
     *
     * @return ConditionParameterInterface
     */
    public function current(): ConditionParameterInterface;

    /**
     * Get a condition by offset.
     *
     * @param mixed $offset
     *
     * @return null|ConditionParameterInterface
     */
    public function offsetGet(mixed $offset): ?ConditionParameterInterface;

    /**
     * @return array<int, array{type_name: string, generic_types?: array{module: string, source_info: array{file: string}}}
     */
    public function jsonSerialize(): array;

    /**
     * @param array<int, array{type_name: string, generic_types?: array{module: string, source_info: array{file: string}}}> $data
     */
    public static function fromArray(array $data): static;
}
