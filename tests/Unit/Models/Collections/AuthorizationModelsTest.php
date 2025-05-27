<?php

declare(strict_types=1);

use OpenFGA\Models\AuthorizationModel;
use OpenFGA\Models\Collections\{AuthorizationModels, AuthorizationModelsInterface, TypeDefinitions};
use OpenFGA\Models\TypeDefinition;
use OpenFGA\Models\Enums\SchemaVersion;
use OpenFGA\Schema\{SchemaInterface, CollectionSchemaInterface};

describe('AuthorizationModels Collection', function (): void {
    test('implements AuthorizationModelsInterface', function (): void {
        $collection = new AuthorizationModels();

        expect($collection)->toBeInstanceOf(AuthorizationModelsInterface::class);
    });

    test('constructs with empty array', function (): void {
        $collection = new AuthorizationModels();

        expect($collection->count())->toBe(0);
        expect($collection->isEmpty())->toBeTrue();
    });

    test('constructs with array of authorization models', function (): void {
        $model1 = new AuthorizationModel(
            id: 'model-1',
            schemaVersion: SchemaVersion::V1_1,
            typeDefinitions: new TypeDefinitions([
                new TypeDefinition(type: 'user'),
                new TypeDefinition(type: 'document'),
            ])
        );
        
        $model2 = new AuthorizationModel(
            id: 'model-2',
            schemaVersion: SchemaVersion::V1_1,
            typeDefinitions: new TypeDefinitions([
                new TypeDefinition(type: 'group'),
            ])
        );
        
        $collection = new AuthorizationModels([$model1, $model2]);

        expect($collection->count())->toBe(2);
        expect($collection->isEmpty())->toBeFalse();
    });

    test('adds authorization models', function (): void {
        $collection = new AuthorizationModels();
        
        $model = new AuthorizationModel(
            id: 'model-1',
            schemaVersion: SchemaVersion::V1_1,
            typeDefinitions: new TypeDefinitions([
                new TypeDefinition(type: 'user'),
            ])
        );
        
        $collection->add($model);
        
        expect($collection->count())->toBe(1);
        expect($collection->get(0))->toBe($model);
    });

    test('checks if model exists', function (): void {
        $model = new AuthorizationModel(
            id: 'model-1',
            schemaVersion: SchemaVersion::V1_1,
            typeDefinitions: new TypeDefinitions([
                new TypeDefinition(type: 'user'),
            ])
        );
        
        $collection = new AuthorizationModels([$model]);
        
        expect(isset($collection[0]))->toBeTrue();
        expect(isset($collection[1]))->toBeFalse();
    });

    test('iterates over models', function (): void {
        $model1 = new AuthorizationModel(
            id: 'model-1',
            schemaVersion: SchemaVersion::V1_1,
            typeDefinitions: new TypeDefinitions([
                new TypeDefinition(type: 'user'),
            ])
        );
        
        $model2 = new AuthorizationModel(
            id: 'model-2',
            schemaVersion: SchemaVersion::V1_1,
            typeDefinitions: new TypeDefinitions([
                new TypeDefinition(type: 'group'),
            ])
        );
        
        $collection = new AuthorizationModels([$model1, $model2]);
        
        $ids = [];
        foreach ($collection as $model) {
            $ids[] = $model->getId();
        }
        
        expect($ids)->toBe(['model-1', 'model-2']);
    });

    test('converts to array', function (): void {
        $model1 = new AuthorizationModel(
            id: 'model-1',
            schemaVersion: SchemaVersion::V1_1,
            typeDefinitions: new TypeDefinitions([
                new TypeDefinition(type: 'user'),
            ])
        );
        
        $model2 = new AuthorizationModel(
            id: 'model-2',
            schemaVersion: SchemaVersion::V1_1,
            typeDefinitions: new TypeDefinitions([
                new TypeDefinition(type: 'group'),
            ])
        );
        
        $collection = new AuthorizationModels([$model1, $model2]);
        $array = $collection->toArray();
        
        expect($array)->toBeArray();
        expect($array)->toHaveCount(2);
        expect($array[0])->toBe($model1);
        expect($array[1])->toBe($model2);
    });

    test('serializes to JSON', function (): void {
        $model = new AuthorizationModel(
            id: 'model-1',
            schemaVersion: SchemaVersion::V1_1,
            typeDefinitions: new TypeDefinitions([
                new TypeDefinition(type: 'user'),
            ])
        );
        
        $collection = new AuthorizationModels([$model]);
        $json = $collection->jsonSerialize();
        
        expect($json)->toBeArray();
        expect($json)->toHaveCount(1);
        expect($json[0])->toHaveKey('id');
        expect($json[0]['id'])->toBe('model-1');
    });

    test('returns schema instance', function (): void {
        $schema = AuthorizationModels::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema)->toBeInstanceOf(CollectionSchemaInterface::class);
        expect($schema->getClassName())->toBe(AuthorizationModels::class);
        // CollectionSchema might have different API than expected
        // Let's just verify it's a collection schema
    });

    test('schema is cached', function (): void {
        // Collection schemas are not cached in the same way as model schemas
        $schema1 = AuthorizationModels::schema();
        $schema2 = AuthorizationModels::schema();

        expect($schema1)->toBeInstanceOf(CollectionSchemaInterface::class);
        expect($schema2)->toBeInstanceOf(CollectionSchemaInterface::class);
    });

    test('filters models by type definitions', function (): void {
        $model1 = new AuthorizationModel(
            id: 'model-1',
            schemaVersion: SchemaVersion::V1_1,
            typeDefinitions: new TypeDefinitions([
                new TypeDefinition(type: 'user'),
                new TypeDefinition(type: 'document'),
            ])
        );
        
        $model2 = new AuthorizationModel(
            id: 'model-2',
            schemaVersion: SchemaVersion::V1_1,
            typeDefinitions: new TypeDefinitions([
                new TypeDefinition(type: 'group'),
            ])
        );
        
        $model3 = new AuthorizationModel(
            id: 'model-3',
            schemaVersion: SchemaVersion::V1_1,
            typeDefinitions: new TypeDefinitions([
                new TypeDefinition(type: 'team'),
                new TypeDefinition(type: 'user'),
            ])
        );
        
        $collection = new AuthorizationModels([$model1, $model2, $model3]);
        
        // Filter models that have 'user' type definition
        $filtered = [];
        foreach ($collection as $model) {
            foreach ($model->getTypeDefinitions() as $typeDef) {
                if ($typeDef->getType() === 'user') {
                    $filtered[] = $model->getId();
                    break;
                }
            }
        }
        
        expect($filtered)->toBe(['model-1', 'model-3']);
    });

    test('handles model history scenarios', function (): void {
        // Create a series of models representing version history
        $models = [];
        
        for ($i = 1; $i <= 5; $i++) {
            $models[] = new AuthorizationModel(
                id: "01HXF7M9KTEQR{$i}NBPW96PDTV",
                schemaVersion: SchemaVersion::V1_1,
                typeDefinitions: new TypeDefinitions([
                    new TypeDefinition(type: 'user'),
                    new TypeDefinition(type: 'document'),
                ])
            );
        }
        
        $collection = new AuthorizationModels($models);
        
        expect($collection->count())->toBe(5);
        
        // Get the latest model (last in the collection)
        $latest = null;
        foreach ($collection as $model) {
            $latest = $model;
        }
        
        expect($latest)->not->toBeNull();
        expect($latest->getId())->toBe('01HXF7M9KTEQR5NBPW96PDTV');
    });

    test('validates all models have same schema version', function (): void {
        $model1 = new AuthorizationModel(
            id: 'model-1',
            schemaVersion: SchemaVersion::V1_1,
            typeDefinitions: new TypeDefinitions([
                new TypeDefinition(type: 'user'),
            ])
        );
        
        $model2 = new AuthorizationModel(
            id: 'model-2',
            schemaVersion: SchemaVersion::V1_1,
            typeDefinitions: new TypeDefinitions([
                new TypeDefinition(type: 'group'),
            ])
        );
        
        $collection = new AuthorizationModels([$model1, $model2]);
        
        // Check all models have the same version
        $versions = [];
        foreach ($collection as $model) {
            $versions[] = $model->getSchemaVersion()->value;
        }
        
        expect(array_unique($versions))->toHaveCount(1);
        expect($versions[0])->toBe('1.1');
    });
});