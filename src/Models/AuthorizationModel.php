<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use InvalidArgumentException;
use OpenFGA\Exceptions\ClientThrowable;
use OpenFGA\Models\Collections\{Conditions, ConditionsInterface, TypeDefinitions, TypeDefinitionsInterface};
use OpenFGA\Models\Enums\SchemaVersion;
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};
use OpenFGA\Transformer;
use Override;
use ReflectionException;

/**
 * Defines the authorization rules and relationships for your application.
 *
 * An AuthorizationModel is the core configuration that tells OpenFGA how
 * permissions work in your system. It defines object types (like documents, folders),
 * relationships (like owner, editor, viewer), and the rules for how those
 * relationships grant access.
 *
 * Think of this as your application's "permission blueprint" - it describes
 * all the ways users can be related to objects and what those relationships mean
 * for access control decisions.
 */
final class AuthorizationModel implements AuthorizationModelInterface
{
    public const string OPENAPI_MODEL = 'AuthorizationModel';

    private static ?SchemaInterface $schema = null;

    /**
     * @param string                                            $id              authorization model ID
     * @param SchemaVersion                                     $schemaVersion   schema version of the authorization model
     * @param TypeDefinitionsInterface<TypeDefinitionInterface> $typeDefinitions type definitions for the authorization model
     * @param ConditionsInterface<ConditionInterface>|null      $conditions      conditions for the authorization model
     */
    public function __construct(
        private readonly string $id,
        private readonly SchemaVersion $schemaVersion,
        private readonly TypeDefinitionsInterface $typeDefinitions,
        private readonly ?ConditionsInterface $conditions = null,
    ) {
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public static function schema(): SchemaInterface
    {
        return self::$schema ??= new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'id', type: 'string', required: true),
                new SchemaProperty(name: 'schema_version', type: 'string', required: true),
                new SchemaProperty(name: 'type_definitions', type: 'object', className: TypeDefinitions::class, required: true),
                new SchemaProperty(name: 'conditions', type: 'object', className: Conditions::class, required: false),
            ],
        );
    }

    /**
     * @inheritDoc
     *
     * @throws ClientThrowable          If the authorization model cannot be converted to DSL format
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    #[Override]
    public function dsl(): string
    {
        return Transformer::toDsl($this);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getConditions(): ?ConditionsInterface
    {
        return $this->conditions;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getSchemaVersion(): SchemaVersion
    {
        return $this->schemaVersion;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getTypeDefinitions(): TypeDefinitionsInterface
    {
        return $this->typeDefinitions;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function jsonSerialize(): array
    {
        /** @var array{conditions?: array<int, array{expression: string, metadata?: array<string, mixed>, name: string, parameters?: array<string, mixed>}>, id: string, schema_version: string, type_definitions: array<int, array{metadata?: array<string, mixed>, relations?: array<string, mixed>, type: string}>} */
        return array_filter([
            'id' => $this->id,
            'schema_version' => $this->schemaVersion->value,
            'type_definitions' => $this->typeDefinitions->jsonSerialize(),
            'conditions' => $this->conditions?->jsonSerialize(),
        ], static fn ($value): bool => null !== $value);
    }
}
