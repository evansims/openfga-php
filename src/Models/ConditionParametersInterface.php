<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use JsonSerializable;

/**
 * @psalm-type ConditionParametersShape = list<ConditionParameterShape>
 */
interface ConditionParametersInterface extends CollectionInterface, JsonSerializable
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
     * @return ConditionParametersShape
     */
    public function jsonSerialize(): array;

    /**
     * Get a condition by offset.
     *
     * @param mixed $offset
     *
     * @return null|ConditionParameterInterface
     */
    public function offsetGet(mixed $offset): ?ConditionParameterInterface;

    /**
     * @param ConditionParametersShape $data
     */
    public static function fromArray(array $data): static;
}
