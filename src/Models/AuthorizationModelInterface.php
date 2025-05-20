<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Models\Collections\{ConditionsInterface, TypeDefinitionsInterface};
use OpenFGA\Models\Enums\SchemaVersion;

interface AuthorizationModelInterface extends ModelInterface
{
    /**
     * @return null|ConditionsInterface<ConditionInterface>
     */
    public function getConditions(): ?ConditionsInterface;

    public function getId(): string;

    public function getSchemaVersion(): SchemaVersion;

    /**
     * @return TypeDefinitionsInterface<TypeDefinitionInterface>
     */
    public function getTypeDefinitions(): TypeDefinitionsInterface;

    /**
     * @return array{
     *     id: string,
     *     schema_version: string,
     *     type_definitions: array<int, array{type: string, relations?: array<string, mixed>, metadata?: array<string, mixed>}>,
     *     conditions?: array<int, array{name: string, expression: string, parameters?: array<string, mixed>, metadata?: array<string, mixed>}>
     * }
     */
    public function jsonSerialize(): array;
}
