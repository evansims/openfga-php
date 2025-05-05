<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class AuthorizationModel extends Model implements AuthorizationModelInterface
{
    public function __construct(
        public string $id,
        public string $schemaVersion,
        public TypeDefinitions $typeDefinitions,
        public ?Conditions $conditions = null,
    ) {
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
