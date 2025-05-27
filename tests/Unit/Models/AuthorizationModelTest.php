<?php

declare(strict_types=1);

use OpenFGA\Models\{AuthorizationModel, AuthorizationModelInterface, Condition, ConditionParameter, ObjectRelation, TypeDefinition, Userset};
use OpenFGA\Models\Collections\{ConditionParameters, Conditions, TypeDefinitionRelations, TypeDefinitions};
use OpenFGA\Models\Enums\SchemaVersion;
use OpenFGA\Schema\SchemaInterface;

describe('AuthorizationModel Model', function (): void {
    test('implements AuthorizationModelInterface', function (): void {
        $typeDefinitions = new TypeDefinitions([]);

        $model = new AuthorizationModel(
            id: 'model-123',
            schemaVersion: SchemaVersion::V1_1,
            typeDefinitions: $typeDefinitions,
        );

        expect($model)->toBeInstanceOf(AuthorizationModelInterface::class);
    });

    test('constructs with required parameters only', function (): void {
        $typeDefinitions = new TypeDefinitions([]);

        $model = new AuthorizationModel(
            id: 'model-123',
            schemaVersion: SchemaVersion::V1_1,
            typeDefinitions: $typeDefinitions,
        );

        expect($model->getId())->toBe('model-123');
        expect($model->getSchemaVersion())->toBe(SchemaVersion::V1_1);
        expect($model->getTypeDefinitions())->toBe($typeDefinitions);
        expect($model->getConditions())->toBeNull();
    });

    test('constructs with conditions', function (): void {
        $typeDefinitions = new TypeDefinitions([]);
        $conditions = new Conditions([]);

        $model = new AuthorizationModel(
            id: 'model-123',
            schemaVersion: SchemaVersion::V1_1,
            typeDefinitions: $typeDefinitions,
            conditions: $conditions,
        );

        expect($model->getConditions())->toBe($conditions);
    });

    test('constructs with full type definition', function (): void {
        // Create a simple type definition for 'user'
        $userType = new TypeDefinition(
            type: 'user',
            relations: new TypeDefinitionRelations([]),
        );

        // Create a type definition for 'document' with a viewer relation
        $documentRelations = new TypeDefinitionRelations([]);
        $viewerRelation = new Userset(
            this: new ObjectRelation(relation: 'viewer'),
        );
        $documentRelations->set('viewer', $viewerRelation);

        $documentType = new TypeDefinition(
            type: 'document',
            relations: $documentRelations,
        );

        $typeDefinitions = new TypeDefinitions();
        $typeDefinitions->add($userType);
        $typeDefinitions->add($documentType);

        $model = new AuthorizationModel(
            id: 'model-123',
            schemaVersion: SchemaVersion::V1_1,
            typeDefinitions: $typeDefinitions,
        );

        expect($model->getTypeDefinitions()->count())->toBe(2);
        expect($model->getTypeDefinitions()->get('user'))->toBe($userType);
        expect($model->getTypeDefinitions()->get('document'))->toBe($documentType);
    });

    test('serializes to JSON without conditions', function (): void {
        $typeDefinitions = new TypeDefinitions([]);

        $model = new AuthorizationModel(
            id: 'model-123',
            schemaVersion: SchemaVersion::V1_1,
            typeDefinitions: $typeDefinitions,
        );

        $json = $model->jsonSerialize();

        expect($json)->toBe([
            'id' => 'model-123',
            'schema_version' => '1.1',
            'type_definitions' => [],
        ]);
        expect($json)->not->toHaveKey('conditions');
    });

    test('serializes to JSON with conditions', function (): void {
        $typeDefinitions = new TypeDefinitions([]);

        $conditionParam = new ConditionParameter(
            name: 'region',
            value: 'string',
        );
        $conditionParams = new ConditionParameters([$conditionParam]);

        $condition = new Condition(
            name: 'inRegion',
            expression: 'params.region == "us-east"',
            parameters: $conditionParams,
        );

        $conditions = new Conditions([$condition]);

        $model = new AuthorizationModel(
            id: 'model-123',
            schemaVersion: SchemaVersion::V1_1,
            typeDefinitions: $typeDefinitions,
            conditions: $conditions,
        );

        $json = $model->jsonSerialize();

        expect($json)->toHaveKeys(['id', 'schema_version', 'type_definitions', 'conditions']);
        expect($json['conditions'])->toBe($conditions->jsonSerialize());
    });

    test('handles different schema versions', function (): void {
        $typeDefinitions = new TypeDefinitions([]);

        $versions = [
            SchemaVersion::V1_1,
        ];

        foreach ($versions as $version) {
            $model = new AuthorizationModel(
                id: 'model-123',
                schemaVersion: $version,
                typeDefinitions: $typeDefinitions,
            );

            expect($model->getSchemaVersion())->toBe($version);
            expect($model->jsonSerialize()['schema_version'])->toBe($version->value);
        }
    });

    test('returns schema instance', function (): void {
        $schema = AuthorizationModel::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema->getClassName())->toBe(AuthorizationModel::class);

        $properties = $schema->getProperties();
        expect($properties)->toHaveCount(4);

        $propertyNames = array_keys($properties);
        expect($propertyNames)->toBe(['id', 'schema_version', 'type_definitions', 'conditions']);
    });

    test('schema properties have correct configuration', function (): void {
        $schema = AuthorizationModel::schema();
        $properties = $schema->getProperties();

        // ID property
        $idProp = $properties[array_keys($properties)[0]];
        expect($idProp->name)->toBe('id');
        expect($idProp->type)->toBe('string');
        expect($idProp->required)->toBe(true);

        // Schema version property
        $versionProp = $properties[array_keys($properties)[1]];
        expect($versionProp->name)->toBe('schema_version');
        expect($versionProp->type)->toBe('string');
        expect($versionProp->required)->toBe(true);

        // Type definitions property
        $typeDefProp = $properties[array_keys($properties)[2]];
        expect($typeDefProp->name)->toBe('type_definitions');
        expect($typeDefProp->type)->toBe('object');
        expect($typeDefProp->className)->toBe(TypeDefinitions::class);
        expect($typeDefProp->required)->toBe(true);

        // Conditions property
        $conditionsProp = $properties[array_keys($properties)[3]];
        expect($conditionsProp->name)->toBe('conditions');
        expect($conditionsProp->type)->toBe('object');
        expect($conditionsProp->className)->toBe(Conditions::class);
        expect($conditionsProp->required)->toBe(false);
    });

    test('schema is cached', function (): void {
        $schema1 = AuthorizationModel::schema();
        $schema2 = AuthorizationModel::schema();

        expect($schema1)->toBe($schema2);
    });

    test('handles empty ID', function (): void {
        $typeDefinitions = new TypeDefinitions([]);

        $model = new AuthorizationModel(
            id: '',
            schemaVersion: SchemaVersion::V1_1,
            typeDefinitions: $typeDefinitions,
        );

        expect($model->getId())->toBe('');
    });

    test('dsl method returns string representation', function (): void {
        $typeDefinitions = new TypeDefinitions([]);

        $model = new AuthorizationModel(
            id: 'model-123',
            schemaVersion: SchemaVersion::V1_1,
            typeDefinitions: $typeDefinitions,
        );

        // The dsl() method calls DslTransformer::toDsl($this)
        // We're just testing that it returns a string and doesn't throw
        $dsl = $model->dsl();

        expect($dsl)->toBeString();
    });

    test('preserves type definition order', function (): void {
        $userType = new TypeDefinition(type: 'user', relations: new TypeDefinitionRelations([]));
        $documentType = new TypeDefinition(type: 'document', relations: new TypeDefinitionRelations([]));
        $folderType = new TypeDefinition(type: 'folder', relations: new TypeDefinitionRelations([]));

        $typeDefinitions = new TypeDefinitions();
        $typeDefinitions->add($userType);
        $typeDefinitions->add($documentType);
        $typeDefinitions->add($folderType);

        $model = new AuthorizationModel(
            id: 'model-123',
            schemaVersion: SchemaVersion::V1_1,
            typeDefinitions: $typeDefinitions,
        );

        $retrievedTypes = $model->getTypeDefinitions();
        expect($retrievedTypes->get('user'))->toBe($userType);
        expect($retrievedTypes->get('document'))->toBe($documentType);
        expect($retrievedTypes->get('folder'))->toBe($folderType);
    });
});
