<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface ConditionInterface extends ModelInterface
{
    public function getExpression(): string;

    public function getMetadata(): ?ConditionMetadataInterface;

    public function getName(): string;

    public function getParameters(): ?ConditionParametersInterface;
}
