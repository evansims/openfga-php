<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models;

use InvalidArgumentException;
use OpenFGA\Models\{ObjectRelation, ObjectRelationInterface};
use OpenFGA\Schemas\SchemaInterface;
use TypeError;

describe('ObjectRelation Model', function (): void {
    test('implements ObjectRelationInterface', function (): void {
        $objectRelation = new ObjectRelation(relation: 'viewer');

        expect($objectRelation)->toBeInstanceOf(ObjectRelationInterface::class);
    });

    test('constructor throws for missing relation', function (): void {
        expect(fn () => new ObjectRelation)->toThrow(InvalidArgumentException::class, 'Relation cannot be empty.');
    });

    test('constructor throws for missing relation when object is provided', function (): void {
        expect(fn () => new ObjectRelation(object: 'document:1'))->toThrow(InvalidArgumentException::class, 'Relation cannot be empty.');
    });

    test('constructor throws for null relation', function (): void {
        // This will throw TypeError because the constructor expects string, not ?string for relation
        expect(fn () => new ObjectRelation(relation: null))->toThrow(TypeError::class);
        expect(fn () => new ObjectRelation(object: 'test', relation: null))->toThrow(TypeError::class);
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

        // ObjectRelation constructor now requires 'relation'
        foreach ($objects as $object) {
            $objectRelation = new ObjectRelation(object: $object, relation: 'any_relation');
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

    test('serializes to JSON correctly', function (): void {
        // Case 1: Only relation (mandatory)
        $objectRelation1 = new ObjectRelation(relation: 'viewer');
        expect($objectRelation1->jsonSerialize())->toBe(['relation' => 'viewer']);

        // Case 2: Object and relation
        $objectRelation2 = new ObjectRelation(object: 'document:1', relation: 'editor');
        expect($objectRelation2->jsonSerialize())->toBe([
            'object' => 'document:1',
            'relation' => 'editor',
        ]);

        // Case 3: Object is null, relation is present
        $objectRelation3 = new ObjectRelation(object: null, relation: 'owner');
        expect($objectRelation3->jsonSerialize())->toBe(['relation' => 'owner']);

        // Case 4: Object is an empty string (if allowed by your business logic for object, though not typical)
        // Relation is mandatory and non-empty - empty strings are filtered out of JSON serialization
        $objectRelation4 = new ObjectRelation(object: '', relation: 'viewer');
        expect($objectRelation4->jsonSerialize())->toBe(['relation' => 'viewer']);
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
        expect($relationProp->required)->toBe(true);
    });

    test('schema is cached', function (): void {
        $schema1 = ObjectRelation::schema();
        $schema2 = ObjectRelation::schema();

        expect($schema1)->toBe($schema2);
    });

    test('constructor throws for empty relation string', function (): void {
        expect(fn () => new ObjectRelation(relation: ''))->toThrow(InvalidArgumentException::class, 'Relation cannot be empty.');
        expect(fn () => new ObjectRelation(object: 'any_object', relation: ''))->toThrow(InvalidArgumentException::class, 'Relation cannot be empty.');
        // Test with empty object string as well to ensure it's specifically the relation causing the issue
        expect(fn () => new ObjectRelation(object: '', relation: ''))->toThrow(InvalidArgumentException::class, 'Relation cannot be empty.');
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
