<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @psalm-type AuthorizationModelShape = array{id: string, schema_version: string, type_definitions: TypeDefinitionRelationsShape, conditions?: ConditionsShape}
 */
interface AuthorizationModelInterface extends ModelInterface
{
    public function getConditions(): ?ConditionsInterface;

    public function getId(): string;

    public function getSchemaVersion(): string;

    public function getTypeDefinitions(): TypeDefinitionsInterface;

    /**
     * @return AuthorizationModelShape
     */
    public function jsonSerialize(): array;

    /**
     * @param AuthorizationModelShape $data
     */
    public static function fromArray(array $data): static;
}
