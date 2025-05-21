<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Models\Collections\{Conditions, ConditionsInterface, TypeDefinitions, TypeDefinitionsInterface};
use OpenFGA\Models\Enums\SchemaVersion;
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};
use Override;

final class AuthorizationModel implements AuthorizationModelInterface
{
    public const OPENAPI_MODEL = 'AuthorizationModel';

    private static ?SchemaInterface $schema = null;

    /**
     * @param string                                            $id              Authorization model ID.
     * @param SchemaVersion                                     $schemaVersion   Schema version of the authorization model.
     * @param TypeDefinitionsInterface<TypeDefinitionInterface> $typeDefinitions Type definitions for the authorization model.
     * @param null|ConditionsInterface<ConditionInterface>      $conditions      Conditions for the authorization model.
     */
    public function __construct(
        private readonly string $id,
        private readonly SchemaVersion $schemaVersion,
        private readonly TypeDefinitionsInterface $typeDefinitions,
        private readonly ?ConditionsInterface $conditions = null,
    ) {
    }

    #[Override]
    public function getConditions(): ?ConditionsInterface
    {
        return $this->conditions;
    }

    #[Override]
    public function getId(): string
    {
        return $this->id;
    }

    #[Override]
    public function getSchemaVersion(): SchemaVersion
    {
        return $this->schemaVersion;
    }

    #[Override]
    public function getTypeDefinitions(): TypeDefinitionsInterface
    {
        return $this->typeDefinitions;
    }

    #[Override]
    public function jsonSerialize(): array
    {
        return array_filter([
            'id' => $this->id,
            'schema_version' => $this->schemaVersion->value,
            'type_definitions' => $this->typeDefinitions->jsonSerialize(),
            'conditions' => $this->conditions?->jsonSerialize(),
        ], static fn ($value): bool => null !== $value);
    }

    #[Override]
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
