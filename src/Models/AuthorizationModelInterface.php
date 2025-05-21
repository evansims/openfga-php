<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Models\Collections\{ConditionsInterface, TypeDefinitionsInterface};
use OpenFGA\Models\Enums\SchemaVersion;
use Override;

interface AuthorizationModelInterface extends ModelInterface
{
    /**
     * Return a DSL representation of the model.
     */
    public function dsl(): string;

    /**
     * Return the conditions of the model.

     *
     * @return null|ConditionsInterface<ConditionInterface>
     */
    public function getConditions(): ?ConditionsInterface;

    /**
     * Return the ID of the model.
     */
    public function getId(): string;

    /**
     * Return the schema version of the model.
     */
    public function getSchemaVersion(): SchemaVersion;

    /**
     * Return the type definitions of the model.

     *
     * @return TypeDefinitionsInterface<TypeDefinitionInterface>
     */
    public function getTypeDefinitions(): TypeDefinitionsInterface;

    /**
     * Return a JSON representation of the model.

     *
     * @return array{
     *     id: string,
     *     schema_version: string,
     *     type_definitions: array<int, array{type: string, relations?: array<string, mixed>, metadata?: array<string, mixed>}>,
     *     conditions?: array<int, array{name: string, expression: string, parameters?: array<string, mixed>, metadata?: array<string, mixed>}>
     * }
     */
    #[Override]
    public function jsonSerialize(): array;
}
