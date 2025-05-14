<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use InvalidArgumentException;

final class AuthorizationModel implements AuthorizationModelInterface
{
    public function __construct(
        private string $id,
        private string $schemaVersion,
        private TypeDefinitionsInterface $typeDefinitions,
        private ?ConditionsInterface $conditions = null,
    ) {
    }

    public function getConditions(): ?ConditionsInterface
    {
        return $this->conditions;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getSchemaVersion(): string
    {
        return $this->schemaVersion;
    }

    public function getTypeDefinitions(): TypeDefinitionsInterface
    {
        return $this->typeDefinitions;
    }

    public function jsonSerialize(): array
    {
        $response = [
            'id' => $this->id,
            'schema_version' => $this->schemaVersion,
            'type_definitions' => $this->typeDefinitions->jsonSerialize(),
        ];

        if ($this->conditions) {
            $response['conditions'] = $this->conditions->jsonSerialize();
        }

        return $response;
    }

    public static function fromArray(array $data): self
    {
        $data = self::validatedAuthorizationModelShape($data);

        return new self(
            id: $data['id'],
            schemaVersion: $data['schema_version'],
            typeDefinitions: TypeDefinitions::fromArray($data['type_definitions']),
            conditions: isset($data['conditions']) ? Conditions::fromArray($data['conditions']) : null,
        );
    }

    /**
     * @param array{id: string, schema_version: string, type_definitions: TypeDefinitionRelationsShape, conditions?: ConditionsShape} $data
     *
     * @return AuthorizationModelShape
     */
    public static function validatedAuthorizationModelShape(array $data): array
    {
        if (! isset($data['id'])) {
            throw new InvalidArgumentException('Missing id');
        }

        if (! isset($data['schema_version'])) {
            throw new InvalidArgumentException('Missing schema_version');
        }

        if (! isset($data['type_definitions'])) {
            throw new InvalidArgumentException('Missing type_definitions');
        }

        return $data;
    }
}
