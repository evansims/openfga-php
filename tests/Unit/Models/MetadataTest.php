<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models;

use OpenFGA\Models\Collections\RelationMetadataCollection;
use OpenFGA\Models\{Metadata, MetadataInterface, RelationMetadata, SourceInfo};
use OpenFGA\Schema\SchemaInterface;

describe('Metadata Model', function (): void {
    test('implements MetadataInterface', function (): void {
        $metadata = new Metadata();

        expect($metadata)->toBeInstanceOf(MetadataInterface::class);
    });

    test('constructs with all null parameters', function (): void {
        $metadata = new Metadata();

        expect($metadata->getModule())->toBeNull();
        expect($metadata->getRelations())->toBeNull();
        expect($metadata->getSourceInfo())->toBeNull();
    });

    test('constructs with module only', function (): void {
        $metadata = new Metadata(module: 'auth');

        expect($metadata->getModule())->toBe('auth');
        expect($metadata->getRelations())->toBeNull();
        expect($metadata->getSourceInfo())->toBeNull();
    });

    test('constructs with relations', function (): void {
        $sourceInfo = new SourceInfo(file: 'model.fga');
        $relationMetadata = new RelationMetadata(
            module: 'auth',
            sourceInfo: $sourceInfo,
        );

        $relationCollection = new RelationMetadataCollection([
            'viewer' => $relationMetadata,
        ]);

        $metadata = new Metadata(relations: $relationCollection);

        expect($metadata->getRelations())->toBe($relationCollection);
        expect($metadata->getModule())->toBeNull();
        expect($metadata->getSourceInfo())->toBeNull();
    });

    test('constructs with sourceInfo', function (): void {
        $sourceInfo = new SourceInfo(file: 'model.fga');
        $metadata = new Metadata(sourceInfo: $sourceInfo);

        expect($metadata->getSourceInfo())->toBe($sourceInfo);
        expect($metadata->getModule())->toBeNull();
        expect($metadata->getRelations())->toBeNull();
    });

    test('constructs with all parameters', function (): void {
        $sourceInfo = new SourceInfo(file: 'model.fga');
        $relationMetadata = new RelationMetadata(
            module: 'relation_module',
            sourceInfo: $sourceInfo,
        );

        $relationCollection = new RelationMetadataCollection([
            'viewer' => $relationMetadata,
        ]);

        $metadata = new Metadata(
            module: 'main_module',
            relations: $relationCollection,
            sourceInfo: $sourceInfo,
        );

        expect($metadata->getModule())->toBe('main_module');
        expect($metadata->getRelations())->toBe($relationCollection);
        expect($metadata->getSourceInfo())->toBe($sourceInfo);
    });

    test('serializes to JSON with only non-null fields', function (): void {
        $metadata = new Metadata();
        expect($metadata->jsonSerialize())->toBe([]);

        $metadata = new Metadata(module: 'auth');
        expect($metadata->jsonSerialize())->toBe(['module' => 'auth']);

        $sourceInfo = new SourceInfo(file: 'model.fga');
        $metadata = new Metadata(sourceInfo: $sourceInfo);
        expect($metadata->jsonSerialize())->toBe([
            'source_info' => ['file' => 'model.fga'],
        ]);
    });

    test('serializes to JSON with all fields', function (): void {
        $sourceInfo = new SourceInfo(file: 'model.fga');
        $relationMetadata = new RelationMetadata(
            module: 'relation_module',
            sourceInfo: $sourceInfo,
        );

        $relationCollection = new RelationMetadataCollection([
            'viewer' => $relationMetadata,
        ]);

        $metadata = new Metadata(
            module: 'main_module',
            relations: $relationCollection,
            sourceInfo: $sourceInfo,
        );

        $json = $metadata->jsonSerialize();

        expect($json)->toBe([
            'module' => 'main_module',
            'relations' => [
                'viewer' => [
                    'module' => 'relation_module',
                    'source_info' => ['file' => 'model.fga'],
                ],
            ],
            'source_info' => ['file' => 'model.fga'],
        ]);
    });

    test('returns schema instance', function (): void {
        $schema = Metadata::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema->getClassName())->toBe(Metadata::class);

        $properties = $schema->getProperties();
        expect($properties)->toHaveCount(3);

        $propertyNames = array_keys($properties);
        expect($propertyNames)->toBe(['module', 'relations', 'source_info']);
    });

    test('schema properties have correct configuration', function (): void {
        $schema = Metadata::schema();
        $properties = $schema->getProperties();

        // Module property
        $moduleProp = $properties['module'];
        expect($moduleProp->name)->toBe('module');
        expect($moduleProp->type)->toBe('string');
        expect($moduleProp->required)->toBe(false);

        // Relations property
        $relationsProp = $properties['relations'];
        expect($relationsProp->name)->toBe('relations');
        expect($relationsProp->type)->toBe('object');
        expect($relationsProp->required)->toBe(false);

        // SourceInfo property
        $sourceInfoProp = $properties['source_info'];
        expect($sourceInfoProp->name)->toBe('source_info');
        expect($sourceInfoProp->type)->toBe('object');
        expect($sourceInfoProp->required)->toBe(false);
    });

    test('schema is cached', function (): void {
        $schema1 = Metadata::schema();
        $schema2 = Metadata::schema();

        expect($schema1)->toBe($schema2);
    });

    test('handles various module names', function (): void {
        $moduleNames = [
            '',
            'simple',
            'com.example.module',
            'module-with-dashes',
            'module_with_underscores',
            '  module with spaces  ',
        ];

        foreach ($moduleNames as $name) {
            $metadata = new Metadata(module: $name);
            expect($metadata->getModule())->toBe($name);
        }
    });
});
