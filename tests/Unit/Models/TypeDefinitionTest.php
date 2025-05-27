<?php

declare(strict_types=1);

use OpenFGA\Models\Collections\TypeDefinitionRelations;
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

        $metadata = new Metadata(
            relations: $relationMetadata,
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

        expect($json)->toBe(['type' => 'document']);
        expect($json)->not->toHaveKeys(['relations', 'metadata']);
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
        $typeProp = $properties[array_keys($properties)[0]];
        expect($typeProp->name)->toBe('type');
        expect($typeProp->type)->toBe('string');
        expect($typeProp->required)->toBe(true);

        // Relations property
        $relationsProp = $properties[array_keys($properties)[1]];
        expect($relationsProp->name)->toBe('relations');
        expect($relationsProp->type)->toBe('object');
        expect($relationsProp->className)->toBe(TypeDefinitionRelations::class);
        expect($relationsProp->required)->toBe(false);

        // Metadata property
        $metadataProp = $properties[array_keys($properties)[2]];
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
        expect($typeDefinition->getRelations()->count() === 0)->toBe(true);
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
});
