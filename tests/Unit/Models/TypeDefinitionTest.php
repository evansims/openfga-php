<?php

declare(strict_types=1);

use OpenFGA\Models\Collections\{RelationMetadataCollection, TypeDefinitionRelations};
use OpenFGA\Models\{Metadata, ObjectRelation, RelationMetadata, SourceInfo, TypeDefinition, TypeDefinitionInterface, Userset};
use OpenFGA\Schema\SchemaInterface;

describe('TypeDefinition Model', function (): void {
    test('implements TypeDefinitionInterface', function (): void {
        $typeDefinition = new TypeDefinition(type: 'document');

        expect($typeDefinition)->toBeInstanceOf(TypeDefinitionInterface::class);
    });

    test('constructs with type only', function (): void {
        $typeDefinition = new TypeDefinition(type: 'document');

        expect($typeDefinition->getType())->toBe('document');
        expect($typeDefinition->getRelations())->toBeNull();
        expect($typeDefinition->getMetadata())->toBeNull();
    });

    test('constructs with relations', function (): void {
        $relations = new TypeDefinitionRelations([]);
        $viewerRelation = new Userset(computedUserset: new ObjectRelation(relation: 'viewer'));
        $relations->add('viewer', $viewerRelation);

        $typeDefinition = new TypeDefinition(
            type: 'document',
            relations: $relations,
        );

        expect($typeDefinition->getType())->toBe('document');
        expect($typeDefinition->getRelations())->toBe($relations);
        expect($typeDefinition->getRelations()->get('viewer'))->toBe($viewerRelation);
    });

    test('constructs with metadata', function (): void {
        $sourceInfo = new SourceInfo(file: 'model.fga');
        $relationMetadata = new RelationMetadata(
            module: 'auth',
            sourceInfo: $sourceInfo,
        );

        $relationCollection = new RelationMetadataCollection([
            'viewer' => $relationMetadata,
        ]);

        $metadata = new Metadata(
            relations: $relationCollection,
            module: 'auth',
            sourceInfo: $sourceInfo,
        );

        $typeDefinition = new TypeDefinition(
            type: 'document',
            metadata: $metadata,
        );

        expect($typeDefinition->getMetadata())->toBe($metadata);
    });

    test('serializes to JSON with type only', function (): void {
        $typeDefinition = new TypeDefinition(type: 'document');

        $json = $typeDefinition->jsonSerialize();

        expect($json)->toHaveKey('type');
        expect($json['type'])->toBe('document');
        expect($json)->toHaveKey('relations');
        expect($json['relations'])->toBeInstanceOf(stdClass::class);
        expect($json)->not->toHaveKey('metadata');
    });

    test('serializes to JSON with relations', function (): void {
        $relations = new TypeDefinitionRelations([]);
        $viewerRelation = new Userset(computedUserset: new ObjectRelation(relation: 'viewer'));
        $relations->add('viewer', $viewerRelation);

        $typeDefinition = new TypeDefinition(
            type: 'document',
            relations: $relations,
        );

        $json = $typeDefinition->jsonSerialize();

        expect($json)->toHaveKeys(['type', 'relations']);
        expect($json['type'])->toBe('document');
        expect($json['relations'])->toBe($relations->jsonSerialize());
    });

    test('handles different type names', function (): void {
        $types = [
            'user',
            'document',
            'folder',
            'organization',
            'project_resource',
            'api-key',
            'feature:flag',
        ];

        foreach ($types as $typeName) {
            $typeDefinition = new TypeDefinition(type: $typeName);
            expect($typeDefinition->getType())->toBe($typeName);
        }
    });

    test('handles complex relations', function (): void {
        $relations = new TypeDefinitionRelations([]);

        // Add multiple relations
        $relations->add('viewer', new Userset(computedUserset: new ObjectRelation(relation: 'viewer')));
        $relations->add('editor', new Userset(computedUserset: new ObjectRelation(relation: 'editor')));
        $relations->add('owner', new Userset(computedUserset: new ObjectRelation(relation: 'owner')));

        $typeDefinition = new TypeDefinition(
            type: 'document',
            relations: $relations,
        );

        expect($typeDefinition->getRelations()->count())->toBe(3);
        expect($typeDefinition->getRelations()->has('viewer'))->toBe(true);
        expect($typeDefinition->getRelations()->has('editor'))->toBe(true);
        expect($typeDefinition->getRelations()->has('owner'))->toBe(true);
    });

    test('returns schema instance', function (): void {
        $schema = TypeDefinition::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema->getClassName())->toBe(TypeDefinition::class);

        $properties = $schema->getProperties();
        expect($properties)->toHaveCount(3);

        $propertyNames = array_keys($properties);
        expect($propertyNames)->toBe(['type', 'relations', 'metadata']);
    });

    test('schema properties have correct configuration', function (): void {
        $schema = TypeDefinition::schema();
        $properties = $schema->getProperties();

        // Type property
        $typeProp = $properties['type'];
        expect($typeProp->name)->toBe('type');
        expect($typeProp->type)->toBe('string');
        expect($typeProp->required)->toBe(true);

        // Relations property
        $relationsProp = $properties['relations'];
        expect($relationsProp->name)->toBe('relations');
        expect($relationsProp->type)->toBe('object');
        expect($relationsProp->className)->toBe(TypeDefinitionRelations::class);
        expect($relationsProp->required)->toBe(false);

        // Metadata property
        $metadataProp = $properties['metadata'];
        expect($metadataProp->name)->toBe('metadata');
        expect($metadataProp->type)->toBe('object');
        expect($metadataProp->className)->toBe(Metadata::class);
        expect($metadataProp->required)->toBe(false);
    });

    test('schema is cached', function (): void {
        $schema1 = TypeDefinition::schema();
        $schema2 = TypeDefinition::schema();

        expect($schema1)->toBe($schema2);
    });

    test('handles empty type name', function (): void {
        $typeDefinition = new TypeDefinition(type: '');

        expect($typeDefinition->getType())->toBe('');
    });

    test('empty relations collection', function (): void {
        $relations = new TypeDefinitionRelations([]);

        $typeDefinition = new TypeDefinition(
            type: 'document',
            relations: $relations,
        );

        expect($typeDefinition->getRelations())->toBe($relations);
        expect(0 === $typeDefinition->getRelations()->count())->toBe(true);
    });

    test('preserves exact type name without modification', function (): void {
        $typeDefinition = new TypeDefinition(type: '  Document  ');

        expect($typeDefinition->getType())->toBe('  Document  ');
    });

    test('handles type names with special characters', function (): void {
        $specialTypes = [
            'type-with-dash',
            'type_with_underscore',
            'type:with:colon',
            'type/with/slash',
            'type.with.dot',
        ];

        foreach ($specialTypes as $typeName) {
            $typeDefinition = new TypeDefinition(type: $typeName);
            expect($typeDefinition->getType())->toBe($typeName);
        }
    });

    test('serializes metadata when type is not user and metadata implements MetadataInterface', function (): void {
        $sourceInfo = new SourceInfo(file: 'model.fga');
        $relationMetadata = new RelationMetadata(
            module: 'auth',
            sourceInfo: $sourceInfo,
        );

        $relationCollection = new RelationMetadataCollection([
            'viewer' => $relationMetadata,
        ]);

        $metadata = new Metadata(
            relations: $relationCollection,
            module: 'auth',
            sourceInfo: $sourceInfo,
        );

        $typeDefinition = new TypeDefinition(
            type: 'document',
            metadata: $metadata,
        );

        $json = $typeDefinition->jsonSerialize();

        expect($json)->toHaveKey('metadata');
        expect($json['metadata'])->toBe($metadata->jsonSerialize());
    });

    test('omits metadata when type is user even with valid metadata', function (): void {
        $sourceInfo = new SourceInfo(file: 'model.fga');
        $relationMetadata = new RelationMetadata(
            module: 'auth',
            sourceInfo: $sourceInfo,
        );

        $relationCollection = new RelationMetadataCollection([
            'viewer' => $relationMetadata,
        ]);

        $metadata = new Metadata(
            relations: $relationCollection,
            module: 'auth',
            sourceInfo: $sourceInfo,
        );

        $typeDefinition = new TypeDefinition(
            type: 'user',
            metadata: $metadata,
        );

        $json = $typeDefinition->jsonSerialize();

        expect($json)->not->toHaveKey('metadata');
        expect($json)->toHaveKeys(['type', 'relations']);
        expect($json['type'])->toBe('user');
    });

    test('omits metadata when metadata is null', function (): void {
        $typeDefinition = new TypeDefinition(
            type: 'document',
            metadata: null,
        );

        $json = $typeDefinition->jsonSerialize();

        expect($json)->not->toHaveKey('metadata');
        expect($json)->toHaveKeys(['type', 'relations']);
    });

    test('includes metadata for various non-user types', function (): void {
        $sourceInfo = new SourceInfo(file: 'test.fga');
        $metadata = new Metadata(
            module: 'test',
            sourceInfo: $sourceInfo,
        );

        $nonUserTypes = ['document', 'folder', 'organization', 'resource', 'api-key'];

        foreach ($nonUserTypes as $typeName) {
            $typeDefinition = new TypeDefinition(
                type: $typeName,
                metadata: $metadata,
            );

            $json = $typeDefinition->jsonSerialize();

            expect($json)->toHaveKey('metadata');
            expect($json['metadata'])->toBe($metadata->jsonSerialize());
            expect($json['type'])->toBe($typeName);
        }
    });

    test('metadata serialization includes expected structure', function (): void {
        $sourceInfo = new SourceInfo(file: 'auth.fga');
        $relationMetadata = new RelationMetadata(
            module: 'permissions',
            sourceInfo: $sourceInfo,
        );

        $relationCollection = new RelationMetadataCollection([
            'viewer' => $relationMetadata,
            'editor' => $relationMetadata,
        ]);

        $metadata = new Metadata(
            relations: $relationCollection,
            module: 'auth',
            sourceInfo: $sourceInfo,
        );

        $typeDefinition = new TypeDefinition(
            type: 'document',
            metadata: $metadata,
        );

        $json = $typeDefinition->jsonSerialize();

        expect($json)->toHaveKey('metadata');
        expect($json['metadata'])->toBeArray();

        $metadataJson = $json['metadata'];
        expect($metadataJson)->toHaveKey('module');
        expect($metadataJson['module'])->toBe('auth');
        expect($metadataJson)->toHaveKey('source_info');
        expect($metadataJson['source_info'])->toHaveKey('file');
        expect($metadataJson['source_info']['file'])->toBe('auth.fga');
    });

    test('metadata serialization conditional logic coverage', function (): void {
        // Test case 1: Non-user type with metadata (should include)
        $metadata = new Metadata(module: 'test');
        $typeDefinition1 = new TypeDefinition(type: 'document', metadata: $metadata);
        $json1 = $typeDefinition1->jsonSerialize();
        expect($json1)->toHaveKey('metadata');

        // Test case 2: User type with metadata (should omit)
        $typeDefinition2 = new TypeDefinition(type: 'user', metadata: $metadata);
        $json2 = $typeDefinition2->jsonSerialize();
        expect($json2)->not->toHaveKey('metadata');

        // Test case 3: Non-user type without metadata (should omit)
        $typeDefinition3 = new TypeDefinition(type: 'document', metadata: null);
        $json3 = $typeDefinition3->jsonSerialize();
        expect($json3)->not->toHaveKey('metadata');

        // Test case 4: User type without metadata (should omit)
        $typeDefinition4 = new TypeDefinition(type: 'user', metadata: null);
        $json4 = $typeDefinition4->jsonSerialize();
        expect($json4)->not->toHaveKey('metadata');
    });
});
