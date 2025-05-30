<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models;

use OpenFGA\Models\Collections\RelationReferences;
use OpenFGA\Models\{RelationMetadata, RelationMetadataInterface, RelationReference, SourceInfo};
use OpenFGA\Schema\SchemaInterface;

describe('RelationMetadata Model', function (): void {
    test('implements RelationMetadataInterface', function (): void {
        $metadata = new RelationMetadata;

        expect($metadata)->toBeInstanceOf(RelationMetadataInterface::class);
    });

    test('constructs with all null parameters', function (): void {
        $metadata = new RelationMetadata;

        expect($metadata->getModule())->toBeNull();
        expect($metadata->getDirectlyRelatedUserTypes())->toBeNull();
        expect($metadata->getSourceInfo())->toBeNull();
    });

    test('constructs with module only', function (): void {
        $metadata = new RelationMetadata(module: 'auth');

        expect($metadata->getModule())->toBe('auth');
        expect($metadata->getDirectlyRelatedUserTypes())->toBeNull();
        expect($metadata->getSourceInfo())->toBeNull();
    });

    test('constructs with directly related user types', function (): void {
        $userRef = new RelationReference(type: 'user');
        $groupRef = new RelationReference(type: 'group', relation: 'member');
        $references = new RelationReferences([$userRef, $groupRef]);

        $metadata = new RelationMetadata(directlyRelatedUserTypes: $references);

        expect($metadata->getModule())->toBeNull();
        expect($metadata->getDirectlyRelatedUserTypes())->toBe($references);
        expect($metadata->getDirectlyRelatedUserTypes()->count())->toBe(2);
        expect($metadata->getSourceInfo())->toBeNull();
    });

    test('constructs with sourceInfo', function (): void {
        $sourceInfo = new SourceInfo(file: 'relations.fga');
        $metadata = new RelationMetadata(sourceInfo: $sourceInfo);

        expect($metadata->getModule())->toBeNull();
        expect($metadata->getDirectlyRelatedUserTypes())->toBeNull();
        expect($metadata->getSourceInfo())->toBe($sourceInfo);
    });

    test('constructs with all parameters', function (): void {
        $sourceInfo = new SourceInfo(file: 'relations.fga');
        $userRef = new RelationReference(type: 'user');
        $references = new RelationReferences([$userRef]);

        $metadata = new RelationMetadata(
            module: 'auth',
            directlyRelatedUserTypes: $references,
            sourceInfo: $sourceInfo,
        );

        expect($metadata->getModule())->toBe('auth');
        expect($metadata->getDirectlyRelatedUserTypes())->toBe($references);
        expect($metadata->getSourceInfo())->toBe($sourceInfo);
    });

    test('serializes to JSON with only non-null fields', function (): void {
        $metadata = new RelationMetadata;
        expect($metadata->jsonSerialize())->toBe([]);

        $metadata = new RelationMetadata(module: 'auth');
        expect($metadata->jsonSerialize())->toBe(['module' => 'auth']);
    });

    test('serializes to JSON with directly related user types', function (): void {
        $userRef = new RelationReference(type: 'user');
        $groupRef = new RelationReference(type: 'group', relation: 'member');
        $references = new RelationReferences([$userRef, $groupRef]);

        $metadata = new RelationMetadata(directlyRelatedUserTypes: $references);

        $json = $metadata->jsonSerialize();
        expect($json)->toHaveKey('directly_related_user_types');
        expect($json['directly_related_user_types'])->toBe([
            ['type' => 'user'],
            ['type' => 'group', 'relation' => 'member'],
        ]);
    });

    test('serializes to JSON with all fields', function (): void {
        $sourceInfo = new SourceInfo(file: 'relations.fga');
        $userRef = new RelationReference(type: 'user');
        $references = new RelationReferences([$userRef]);

        $metadata = new RelationMetadata(
            module: 'auth',
            directlyRelatedUserTypes: $references,
            sourceInfo: $sourceInfo,
        );

        $json = $metadata->jsonSerialize();
        expect($json)->toBe([
            'module' => 'auth',
            'directly_related_user_types' => [
                ['type' => 'user'],
            ],
            'source_info' => ['file' => 'relations.fga'],
        ]);
    });

    test('returns schema instance', function (): void {
        $schema = RelationMetadata::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema->getClassName())->toBe(RelationMetadata::class);

        $properties = $schema->getProperties();
        expect($properties)->toHaveCount(3);

        $propertyNames = array_keys($properties);
        expect($propertyNames)->toBe(['module', 'directly_related_user_types', 'source_info']);
    });

    test('schema properties have correct configuration', function (): void {
        $schema = RelationMetadata::schema();
        $properties = $schema->getProperties();

        // Module property
        $moduleProp = $properties['module'];
        expect($moduleProp->name)->toBe('module');
        expect($moduleProp->type)->toBe('string');
        expect($moduleProp->required)->toBe(false);

        // DirectlyRelatedUserTypes property
        $userTypesProp = $properties['directly_related_user_types'];
        expect($userTypesProp->name)->toBe('directly_related_user_types');
        expect($userTypesProp->type)->toBe('object');
        expect($userTypesProp->required)->toBe(false);

        // SourceInfo property
        $sourceInfoProp = $properties['source_info'];
        expect($sourceInfoProp->name)->toBe('source_info');
        expect($sourceInfoProp->type)->toBe('object');
        expect($sourceInfoProp->required)->toBe(false);
    });

    test('schema is cached', function (): void {
        $schema1 = RelationMetadata::schema();
        $schema2 = RelationMetadata::schema();

        expect($schema1)->toBe($schema2);
    });

    test('handles empty directly related user types collection', function (): void {
        $references = new RelationReferences([]);
        $metadata = new RelationMetadata(directlyRelatedUserTypes: $references);

        expect($metadata->getDirectlyRelatedUserTypes())->toBe($references);
        expect($metadata->getDirectlyRelatedUserTypes()->isEmpty())->toBe(true);
        expect($metadata->jsonSerialize())->toBe([
            'directly_related_user_types' => [],
        ]);
    });

    test('preserves whitespace in module name', function (): void {
        $metadata = new RelationMetadata(module: '  auth  ');

        expect($metadata->getModule())->toBe('  auth  ');
    });

    test('handles empty module name', function (): void {
        $metadata = new RelationMetadata(module: '');

        expect($metadata->getModule())->toBe('');
        // Empty module string is omitted from JSON serialization
        expect($metadata->jsonSerialize())->toBe([]);
    });
});
