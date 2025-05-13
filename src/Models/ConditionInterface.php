<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface ConditionInterface extends ModelInterface
{
    public function getExpression(): string;

    public function getMetadata(): ?ConditionMetadataInterface;

    public function getName(): string;

    public function getParameters(): ?ConditionParametersInterface;

    /**
     * @return array{name: string, expression: string, parameters?: array{module: string, source_info: array{file: string}}, metadata?: array{module: string, source_info: array{file: string}}}
     */
    public function jsonSerialize(): array;

    /**
     * @param array{name: string, expression: string, parameters?: array{module: string, source_info: array{file: string}}, metadata?: array{module: string, source_info: array{file: string}}} $data
     */
    public static function fromArray(array $data): static;
}
