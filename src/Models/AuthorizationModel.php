<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class AuthorizationModel extends Model implements AuthorizationModelInterface
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

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'schema_version' => $this->schemaVersion,
            'type_definitions' => $this->typeDefinitions->toArray(),
            'conditions' => $this->conditions?->toArray(),
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            schemaVersion: $data['schema_version'],
            typeDefinitions: TypeDefinitions::fromArray($data['type_definitions']),
            conditions: isset($data['conditions']) ? Conditions::fromArray($data['conditions']) : null,
        );
    }
}
