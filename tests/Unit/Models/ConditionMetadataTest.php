<?php

declare(strict_types=1);

use OpenFGA\Models\{ConditionMetadata, ConditionMetadataInterface, SourceInfo};
use OpenFGA\Schema\SchemaInterface;

describe('ConditionMetadata Model', function (): void {
    test('implements ConditionMetadataInterface', function (): void {
        $sourceInfo = new SourceInfo(file: 'conditions.fga');
        $metadata = new ConditionMetadata(module: 'auth', sourceInfo: $sourceInfo);

        expect($metadata)->toBeInstanceOf(ConditionMetadataInterface::class);
    });

    test('constructs with required parameters', function (): void {
        $sourceInfo = new SourceInfo(file: 'conditions.fga');
        $metadata = new ConditionMetadata(module: 'auth', sourceInfo: $sourceInfo);

        expect($metadata->getModule())->toBe('auth');
        expect($metadata->getSourceInfo())->toBe($sourceInfo);
    });

    test('handles various module names', function (): void {
        $sourceInfo = new SourceInfo(file: 'conditions.fga');
        $moduleNames = [
            'auth',
            'core',
            'user-management',
            'com.example.module',
            'module_v2',
            'module123',
        ];

        foreach ($moduleNames as $name) {
            $metadata = new ConditionMetadata(module: $name, sourceInfo: $sourceInfo);
            expect($metadata->getModule())->toBe($name);
        }
    });

    test('serializes to JSON', function (): void {
        $sourceInfo = new SourceInfo(file: 'conditions.fga');
        $metadata = new ConditionMetadata(module: 'auth', sourceInfo: $sourceInfo);

        $json = $metadata->jsonSerialize();

        expect($json)->toBe([
            'module' => 'auth',
            'source_info' => ['file' => 'conditions.fga'],
        ]);
    });

    test('returns schema instance', function (): void {
        $schema = ConditionMetadata::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema->getClassName())->toBe(ConditionMetadata::class);

        $properties = $schema->getProperties();
        expect($properties)->toHaveCount(2);

        $propertyNames = array_keys($properties);
        expect($propertyNames)->toBe(['module', 'source_info']);
    });

    test('schema properties have correct configuration', function (): void {
        $schema = ConditionMetadata::schema();
        $properties = $schema->getProperties();

        // Module property
        $moduleProp = $properties['module'];
        expect($moduleProp->name)->toBe('module');
        expect($moduleProp->type)->toBe('string');
        expect($moduleProp->required)->toBe(true);

        // SourceInfo property
        $sourceInfoProp = $properties['source_info'];
        expect($sourceInfoProp->name)->toBe('source_info');
        expect($sourceInfoProp->type)->toBe(SourceInfo::class);
        expect($sourceInfoProp->required)->toBe(true);
    });

    test('schema is cached', function (): void {
        $schema1 = ConditionMetadata::schema();
        $schema2 = ConditionMetadata::schema();

        expect($schema1)->toBe($schema2);
    });

    test('preserves exact module name', function (): void {
        $sourceInfo = new SourceInfo(file: 'conditions.fga');
        $metadata = new ConditionMetadata(module: '  Auth  ', sourceInfo: $sourceInfo);

        expect($metadata->getModule())->toBe('  Auth  ');
    });

    test('handles empty module name', function (): void {
        $sourceInfo = new SourceInfo(file: 'conditions.fga');
        $metadata = new ConditionMetadata(module: '', sourceInfo: $sourceInfo);

        expect($metadata->getModule())->toBe('');
    });

    test('references to source info are preserved', function (): void {
        $sourceInfo = new SourceInfo(file: 'conditions.fga');
        $metadata = new ConditionMetadata(module: 'auth', sourceInfo: $sourceInfo);

        expect($metadata->getSourceInfo())->toBe($sourceInfo);
        expect($metadata->getSourceInfo()->getFile())->toBe('conditions.fga');
    });
});
