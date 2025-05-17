<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};

final class AuthorizationModel implements AuthorizationModelInterface
{
    private static ?SchemaInterface $schema = null;

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
            'id' => $this->getId(),
            'schema_version' => $this->getSchemaVersion(),
            'type_definitions' => $this->getTypeDefinitions()->jsonSerialize(),
        ];

        if ($this->getConditions()) {
            $response['conditions'] = $this->getConditions()->jsonSerialize();
        }

        return $response;
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
