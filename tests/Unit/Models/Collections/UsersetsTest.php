<?php

declare(strict_types=1);

use OpenFGA\Models\Collections\{Usersets, UsersetsInterface};
use OpenFGA\Models\{DifferenceV1, ObjectRelation, TupleToUsersetV1, Userset};
use OpenFGA\Schema\{CollectionSchemaInterface, SchemaInterface};

describe('Usersets Collection', function (): void {
    test('implements UsersetsInterface', function (): void {
        $collection = new Usersets([]);

        expect($collection)->toBeInstanceOf(UsersetsInterface::class);
    });

    test('constructs with empty array', function (): void {
        $collection = new Usersets([]);

        expect($collection->count())->toBe(0);
        expect($collection->isEmpty())->toBeTrue();
    });

    test('constructs with array of usersets', function (): void {
        $collection = new Usersets([
            'direct' => new Userset(direct: new stdClass()),
            'viewer' => new Userset(computedUserset: new ObjectRelation(relation: 'viewer')),
            'editor' => new Userset(computedUserset: new ObjectRelation(relation: 'editor')),
        ]);

        expect($collection->count())->toBe(3);
        expect($collection->isEmpty())->toBeFalse();
    });

    test('adds usersets with key', function (): void {
        $collection = new Usersets([]);

        $userset = new Userset(direct: new stdClass());

        $collection->add('users', $userset);

        expect($collection->count())->toBe(1);
        expect($collection->get('users'))->toBe($userset);
    });

    test('gets usersets by key', function (): void {
        $userset1 = new Userset(direct: new stdClass());
        $userset2 = new Userset(computedUserset: new ObjectRelation(relation: 'owner'));

        $collection = new Usersets([
            'direct' => $userset1,
            'computed' => $userset2,
        ]);

        expect($collection->get('direct'))->toBe($userset1);
        expect($collection->get('computed'))->toBe($userset2);
        expect($collection->get('nonexistent'))->toBeNull();
    });

    test('checks if userset exists by key', function (): void {
        $userset = new Userset(direct: new stdClass());

        $collection = new Usersets([
            'users' => $userset,
        ]);

        expect(isset($collection['users']))->toBeTrue();
        expect(isset($collection['other']))->toBeFalse();
    });

    test('iterates over usersets', function (): void {
        $collection = new Usersets([
            'direct' => new Userset(direct: new stdClass()),
            'viewer' => new Userset(computedUserset: new ObjectRelation(relation: 'viewer')),
            'editor' => new Userset(computedUserset: new ObjectRelation(relation: 'editor')),
        ]);

        $keys = [];
        foreach ($collection as $key => $userset) {
            $keys[] = $key;
            expect($userset)->toBeInstanceOf(Userset::class);
        }

        expect($keys)->toBe(['direct', 'viewer', 'editor']);
    });

    test('converts to array', function (): void {
        $userset1 = new Userset(direct: new stdClass());
        $userset2 = new Userset(computedUserset: new ObjectRelation(relation: 'viewer'));

        $collection = new Usersets([
            'direct' => $userset1,
            'computed' => $userset2,
        ]);

        $array = $collection->toArray();

        expect($array)->toBeArray();
        expect($array)->toHaveCount(2);
        expect($array['direct'])->toBe($userset1);
        expect($array['computed'])->toBe($userset2);
    });

    test('serializes to JSON as object', function (): void {
        $collection = new Usersets([
            'direct' => new Userset(direct: new stdClass()),
            'viewer' => new Userset(computedUserset: new ObjectRelation(relation: 'viewer')),
            'difference' => new Userset(
                difference: new DifferenceV1(
                    base: new Userset(computedUserset: new ObjectRelation(relation: 'editor')),
                    subtract: new Userset(computedUserset: new ObjectRelation(relation: 'blocked')),
                ),
            ),
        ]);

        $json = $collection->jsonSerialize();

        expect($json)->toBeArray();
        expect($json)->toHaveCount(3);

        // Check direct userset
        expect($json)->toHaveKey('direct');
        expect($json['direct'])->toHaveKey('direct');
        expect($json['direct']['direct'])->toBeInstanceOf(stdClass::class);

        // Check viewer userset
        expect($json)->toHaveKey('viewer');
        expect($json['viewer'])->toHaveKey('computed_userset');
        expect($json['viewer']['computed_userset'])->toBe(['relation' => 'viewer']);

        // Check difference userset
        expect($json)->toHaveKey('difference');
        expect($json['difference'])->toHaveKey('difference');
        expect($json['difference']['difference'])->toHaveKey('base');
        expect($json['difference']['difference'])->toHaveKey('subtract');
    });

    test('handles different userset types', function (): void {
        $collection = new Usersets([
            // Direct assignment
            'direct' => new Userset(direct: new stdClass()),

            // Computed userset
            'computed' => new Userset(computedUserset: new ObjectRelation(relation: 'viewer')),

            // Union of usersets
            'union' => new Userset(
                union: new Usersets([
                    'u1' => new Userset(direct: new stdClass()),
                    'u2' => new Userset(computedUserset: new ObjectRelation(relation: 'viewer')),
                ]),
            ),

            // Intersection of usersets
            'intersection' => new Userset(
                intersection: new Usersets([
                    'i1' => new Userset(computedUserset: new ObjectRelation(relation: 'editor')),
                    'i2' => new Userset(computedUserset: new ObjectRelation(relation: 'active')),
                ]),
            ),

            // Difference of usersets
            'difference' => new Userset(
                difference: new DifferenceV1(
                    base: new Userset(computedUserset: new ObjectRelation(relation: 'member')),
                    subtract: new Userset(computedUserset: new ObjectRelation(relation: 'banned')),
                ),
            ),

            // Tuple to userset
            'tupleToUserset' => new Userset(
                tupleToUserset: new TupleToUsersetV1(
                    tupleset: new ObjectRelation(relation: 'parent'),
                    computedUserset: new ObjectRelation(relation: 'owner'),
                ),
            ),
        ]);

        expect($collection->count())->toBe(6);

        // Verify we can access each by key
        expect($collection->get('direct'))->toBeInstanceOf(Userset::class);
        expect($collection->get('computed'))->toBeInstanceOf(Userset::class);
        expect($collection->get('union'))->toBeInstanceOf(Userset::class);
        expect($collection->get('intersection'))->toBeInstanceOf(Userset::class);
        expect($collection->get('difference'))->toBeInstanceOf(Userset::class);
        expect($collection->get('tupleToUserset'))->toBeInstanceOf(Userset::class);
    });

    test('returns schema instance', function (): void {
        $schema = Usersets::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema)->toBeInstanceOf(CollectionSchemaInterface::class);
        expect($schema->getClassName())->toBe(Usersets::class);
    });

    test('schema is cached', function (): void {
        $schema1 = Usersets::schema();
        $schema2 = Usersets::schema();

        expect($schema1)->toBeInstanceOf(CollectionSchemaInterface::class);
        expect($schema2)->toBeInstanceOf(CollectionSchemaInterface::class);
        expect($schema1)->toBe($schema2);
    });

    test('handles empty collection edge cases', function (): void {
        $collection = new Usersets([]);

        expect($collection->isEmpty())->toBeTrue();
        expect($collection->toArray())->toBe([]);
        expect($collection->jsonSerialize())->toBe([]);

        // Test iteration on empty collection
        $count = 0;
        foreach ($collection as $_) {
            ++$count;
        }
        expect($count)->toBe(0);

        // Test get on empty collection
        expect($collection->get('any'))->toBeNull();
    });
});
