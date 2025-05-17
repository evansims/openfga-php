<?php

declare(strict_types=1);

use OpenFGA\Models\{AuthorizationModel, TypeDefinition, TypeDefinitionRelations, TypeDefinitions, Userset};

beforeEach(function (): void {
    // Create a simple type definition for testing
    $this->typeDefinitions = new TypeDefinitions([
        new TypeDefinition(
            'document',
            new TypeDefinitionRelations([
                'reader' => new Userset((object) ['this' => (object) []]),
                'writer' => new Userset((object) ['this' => (object) []]),
            ]),
        ),
    ]);

    $this->modelId = '01H0S9V8X3Y5Z4W7R8T9Q0W1E2';
    $this->schemaVersion = '1.1';
});

test('constructor and getters', function (): void {
    $model = new AuthorizationModel(
        id: $this->modelId,
        schemaVersion: $this->schemaVersion,
        typeDefinitions: $this->typeDefinitions,
    );

    expect($model->getId())->toBe($this->modelId)
        ->and($model->getSchemaVersion())->toBe($this->schemaVersion)
        ->and($model->getTypeDefinitions())->toBe($this->typeDefinitions)
        ->and($model->getConditions())->toBeNull();
});

test('json serialize with all properties', function (): void {
    $model = new AuthorizationModel(
        id: $this->modelId,
        schemaVersion: $this->schemaVersion,
        typeDefinitions: $this->typeDefinitions,
        conditions: null,
    );

    $result = $model->jsonSerialize();

    expect($result)->toMatchArray([
        'id' => $this->modelId,
        'schema_version' => $this->schemaVersion,
        'type_definitions' => $this->typeDefinitions->jsonSerialize(),
    ]);
});

test('schema', function (): void {
    $schema = AuthorizationModel::schema();

    expect($schema->getClassName())->toBe(AuthorizationModel::class);

    $properties = $schema->getProperties();
    expect($properties)->toHaveKeys(['id', 'schema_version', 'type_definitions', 'conditions']);

    expect($properties['id']->required)->toBeTrue()
        ->and($properties['id']->type)->toBe('string');

    expect($properties['schema_version']->required)->toBeTrue()
        ->and($properties['schema_version']->type)->toBe('string');

    expect($properties['type_definitions']->required)->toBeTrue()
        ->and($properties['type_definitions']->type)->toBe(TypeDefinitions::class);

    expect($properties['conditions']->required)->toBeFalse()
        ->and($properties['conditions']->type)->toBe('OpenFGA\\Models\\Conditions');
});
