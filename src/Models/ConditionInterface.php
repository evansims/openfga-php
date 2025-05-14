<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @psalm-type ConditionShape = array{name: string, expression: string, parameters?: ConditionParametersShape, metadata?: ConditionMetadataShape}
 */
interface ConditionInterface extends ModelInterface
{
    public function getExpression(): string;

    public function getMetadata(): ?ConditionMetadataInterface;

    public function getName(): string;

    public function getParameters(): ?ConditionParametersInterface;

    /**
     * @return ConditionShape
     */
    public function jsonSerialize(): array;

    /**
     * @param ConditionShape $data
     */
    public static function fromArray(array $data): static;
}
