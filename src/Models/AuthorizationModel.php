<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class AuthorizationModel implements AuthorizationModelInterface
{
    use ModelTrait;

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
        assert(isset($data['id']), 'Missing id');
        assert(isset($data['schema_version']), 'Missing schema_version');
        assert(isset($data['type_definitions']), 'Missing type_definitions');

        return new self(
            id: $data['id'],
            schemaVersion: $data['schema_version'],
            typeDefinitions: TypeDefinitions::fromArray($data['type_definitions']),
            conditions: isset($data['conditions']) ? Conditions::fromArray($data['conditions']) : null,
        );
    }
}
