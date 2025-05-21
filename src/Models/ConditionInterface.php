<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Models\Collections\ConditionParametersInterface;
use Override;

interface ConditionInterface extends ModelInterface
{
    public function getExpression(): string;

    public function getMetadata(): ?ConditionMetadataInterface;

    public function getName(): string;

    /**
     * @return null|ConditionParametersInterface<ConditionParameterInterface>
     */
    public function getParameters(): ?ConditionParametersInterface;

    /**
     * @return array{name: string, expression: string, parameters?: list<array{type_name: string, generic_types?: mixed}>, metadata?: array{module: string, source_info: array{file: string}}}
     */
    #[Override]
    public function jsonSerialize(): array;
}
