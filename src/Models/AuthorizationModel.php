<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};

final class AuthorizationModel implements AuthorizationModelInterface
{
    private static ?SchemaInterface $schema = null;

    public function __construct(
        private readonly string $id,
        private readonly string $schemaVersion,
        private readonly TypeDefinitionsInterface $typeDefinitions,
        private readonly ?ConditionsInterface $conditions = null,
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

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_filter([
            'id' => $this->id,
            'schema_version' => $this->schemaVersion,
            'type_definitions' => $this->typeDefinitions->jsonSerialize(),
            'conditions' => $this->conditions?->jsonSerialize(),
        ], static fn ($value) => null !== $value);
    }

    public static function schema(): SchemaInterface
    {
        return self::$schema ??= new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'id', type: 'string', required: true),
                new SchemaProperty(name: 'schema_version', type: 'string', required: true),
                new SchemaProperty(name: 'type_definitions', type: TypeDefinitions::class, required: true),
                new SchemaProperty(name: 'conditions', type: Conditions::class, required: false),
            ],
        );
    }
}
