<?php

declare(strict_types=1);

use OpenFGA\Models\{ObjectRelation, ObjectRelationInterface};
use OpenFGA\Schema\SchemaInterface;

describe('ObjectRelation Model', function (): void {
    test('implements ObjectRelationInterface', function (): void {
        $objectRelation = new ObjectRelation();

        expect($objectRelation)->toBeInstanceOf(ObjectRelationInterface::class);
    });

    test('constructs with null parameters', function (): void {
        $objectRelation = new ObjectRelation();

        expect($objectRelation->getObject())->toBeNull();
        expect($objectRelation->getRelation())->toBeNull();
    });

    test('constructs with object only', function (): void {
        $objectRelation = new ObjectRelation(object: 'document:1');

        expect($objectRelation->getObject())->toBe('document:1');
        expect($objectRelation->getRelation())->toBeNull();
    });

    test('constructs with relation only', function (): void {
        $objectRelation = new ObjectRelation(relation: 'viewer');

        expect($objectRelation->getObject())->toBeNull();
        expect($objectRelation->getRelation())->toBe('viewer');
    });

    test('constructs with both object and relation', function (): void {
        $objectRelation = new ObjectRelation(
            object: 'document:1',
            relation: 'viewer',
        );

        expect($objectRelation->getObject())->toBe('document:1');
        expect($objectRelation->getRelation())->toBe('viewer');
    });

    test('handles various object formats', function (): void {
        $objects = [
            'user:1',
            'document:abc-123',
            'folder:root',
            'group:admin',
            'organization:acme-corp',
            'resource:uuid-v4-here',
            'type:id with spaces',
            'type:id/with/slashes',
        ];

        foreach ($objects as $object) {
            $objectRelation = new ObjectRelation(object: $object);
            expect($objectRelation->getObject())->toBe($object);
        }
    });

    test('handles various relation names', function (): void {
        $relations = [
            'viewer',
            'editor',
            'owner',
            'member',
            'admin',
            'can_view',
            'can_edit',
            'parent',
            'child',
        ];

        foreach ($relations as $relation) {
            $objectRelation = new ObjectRelation(relation: $relation);
            expect($objectRelation->getRelation())->toBe($relation);
        }
    });

    test('serializes to JSON with only non-null fields', function (): void {
        $objectRelation = new ObjectRelation();
        expect($objectRelation->jsonSerialize())->toBe([]);

        $objectRelation = new ObjectRelation(object: 'document:1');
        expect($objectRelation->jsonSerialize())->toBe(['object' => 'document:1']);

        $objectRelation = new ObjectRelation(relation: 'viewer');
        expect($objectRelation->jsonSerialize())->toBe(['relation' => 'viewer']);
    });

    test('serializes to JSON with all fields', function (): void {
        $objectRelation = new ObjectRelation(
            object: 'document:1',
            relation: 'viewer',
        );

        $json = $objectRelation->jsonSerialize();

        expect($json)->toBe([
            'object' => 'document:1',
            'relation' => 'viewer',
        ]);
    });

    test('returns schema instance', function (): void {
        $schema = ObjectRelation::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema->getClassName())->toBe(ObjectRelation::class);

        $properties = $schema->getProperties();
        expect($properties)->toHaveCount(2);

        $propertyNames = array_keys($properties);
        expect($propertyNames)->toBe(['object', 'relation']);
    });

    test('schema properties have correct configuration', function (): void {
        $schema = ObjectRelation::schema();
        $properties = $schema->getProperties();

        // Object property
        $objectProp = $properties['object'];
        expect($objectProp->name)->toBe('object');
        expect($objectProp->type)->toBe('string');
        expect($objectProp->required)->toBe(false);

        // Relation property
        $relationProp = $properties['relation'];
        expect($relationProp->name)->toBe('relation');
        expect($relationProp->type)->toBe('string');
        expect($relationProp->required)->toBe(false);
    });

    test('schema is cached', function (): void {
        $schema1 = ObjectRelation::schema();
        $schema2 = ObjectRelation::schema();

        expect($schema1)->toBe($schema2);
    });

    test('preserves empty strings', function (): void {
        $objectRelation = new ObjectRelation(
            object: '',
            relation: '',
        );

        expect($objectRelation->getObject())->toBe('');
        expect($objectRelation->getRelation())->toBe('');
        // Empty strings are omitted from JSON serialization
        expect($objectRelation->jsonSerialize())->toBe([]);
    });

    test('preserves whitespace', function (): void {
        $objectRelation = new ObjectRelation(
            object: '  document:1  ',
            relation: '  viewer  ',
        );

        expect($objectRelation->getObject())->toBe('  document:1  ');
        expect($objectRelation->getRelation())->toBe('  viewer  ');
    });

    test('handles unicode characters', function (): void {
        $objectRelation = new ObjectRelation(
            object: 'документ:1',
            relation: 'читатель',
        );

        expect($objectRelation->getObject())->toBe('документ:1');
        expect($objectRelation->getRelation())->toBe('читатель');
    });
});
