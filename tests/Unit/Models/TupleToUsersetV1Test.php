<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models;

use OpenFGA\Models\{ObjectRelation, TupleToUsersetV1, TupleToUsersetV1Interface};
use OpenFGA\Schema\SchemaInterface;

describe('TupleToUsersetV1 Model', function (): void {
    test('implements TupleToUsersetV1Interface', function (): void {
        $tupleset = new ObjectRelation(relation: 'parent');
        $computedUserset = new ObjectRelation(relation: 'viewer');
        $tupleToUserset = new TupleToUsersetV1(
            tupleset: $tupleset,
            computedUserset: $computedUserset,
        );

        expect($tupleToUserset)->toBeInstanceOf(TupleToUsersetV1Interface::class);
    });

    test('constructs with tupleset and computed userset', function (): void {
        $tupleset = new ObjectRelation(relation: 'parent');
        $computedUserset = new ObjectRelation(relation: 'viewer');

        $tupleToUserset = new TupleToUsersetV1(
            tupleset: $tupleset,
            computedUserset: $computedUserset,
        );

        expect($tupleToUserset->getTupleset())->toBe($tupleset);
        expect($tupleToUserset->getComputedUserset())->toBe($computedUserset);
    });

    test('serializes to JSON', function (): void {
        $tupleset = new ObjectRelation(relation: 'parent');
        $computedUserset = new ObjectRelation(relation: 'viewer');

        $tupleToUserset = new TupleToUsersetV1(
            tupleset: $tupleset,
            computedUserset: $computedUserset,
        );

        expect($tupleToUserset->jsonSerialize())->toBe([
            'tupleset' => ['relation' => 'parent'],
            'computedUserset' => ['relation' => 'viewer'],
        ]);
    });

    test('handles complex object relations', function (): void {
        // Tupleset with object
        $tupleset = new ObjectRelation(
            object: 'folder',
            relation: 'parent',
        );

        // Computed userset with object
        $computedUserset = new ObjectRelation(
            object: 'document',
            relation: 'owner',
        );

        $tupleToUserset = new TupleToUsersetV1(
            tupleset: $tupleset,
            computedUserset: $computedUserset,
        );

        expect($tupleToUserset->jsonSerialize())->toBe([
            'tupleset' => [
                'object' => 'folder',
                'relation' => 'parent',
            ],
            'computedUserset' => [
                'object' => 'document',
                'relation' => 'owner',
            ],
        ]);
    });

    test('returns schema instance', function (): void {
        $schema = TupleToUsersetV1::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema->getClassName())->toBe(TupleToUsersetV1::class);

        $properties = $schema->getProperties();
        expect($properties)->toHaveCount(2);

        $propertyNames = array_keys($properties);
        expect($propertyNames)->toBe(['tupleset', 'computedUserset']);
    });

    test('schema properties have correct configuration', function (): void {
        $schema = TupleToUsersetV1::schema();
        $properties = $schema->getProperties();

        // Tupleset property
        $tuplesetProp = $properties['tupleset'];
        expect($tuplesetProp->name)->toBe('tupleset');
        expect($tuplesetProp->type)->toBe('object');
        expect($tuplesetProp->required)->toBe(true);

        // ComputedUserset property
        $computedUsersetProp = $properties['computedUserset'];
        expect($computedUsersetProp->name)->toBe('computedUserset');
        expect($computedUsersetProp->type)->toBe('object');
        expect($computedUsersetProp->required)->toBe(true);
    });

    test('schema is cached', function (): void {
        $schema1 = TupleToUsersetV1::schema();
        $schema2 = TupleToUsersetV1::schema();

        expect($schema1)->toBe($schema2);
    });

    test('handles typical authorization patterns', function (): void {
        // Pattern 1: Viewers of parent folder can view documents
        $folderViewers = new TupleToUsersetV1(
            tupleset: new ObjectRelation(relation: 'parent'),
            computedUserset: new ObjectRelation(relation: 'viewer'),
        );

        $json = $folderViewers->jsonSerialize();
        expect($json['tupleset']['relation'])->toBe('parent');
        expect($json['computedUserset']['relation'])->toBe('viewer');

        // Pattern 2: Owners of organization can manage projects
        $orgOwners = new TupleToUsersetV1(
            tupleset: new ObjectRelation(relation: 'organization'),
            computedUserset: new ObjectRelation(relation: 'owner'),
        );

        $json2 = $orgOwners->jsonSerialize();
        expect($json2['tupleset']['relation'])->toBe('organization');
        expect($json2['computedUserset']['relation'])->toBe('owner');

        // Pattern 3: Members of team can access resources
        $teamMembers = new TupleToUsersetV1(
            tupleset: new ObjectRelation(relation: 'team'),
            computedUserset: new ObjectRelation(relation: 'member'),
        );

        $json3 = $teamMembers->jsonSerialize();
        expect($json3['tupleset']['relation'])->toBe('team');
        expect($json3['computedUserset']['relation'])->toBe('member');
    });
});
