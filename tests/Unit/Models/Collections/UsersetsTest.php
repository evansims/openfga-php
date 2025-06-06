<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models\Collections;

use OpenFGA\Models\Collections\{Usersets, UsersetsInterface};
use OpenFGA\Models\{DifferenceV1, ObjectRelation, TupleToUsersetV1, Userset};
use OpenFGA\Schema\{CollectionSchemaInterface, SchemaInterface};
use stdClass;

describe('Usersets Collection', function (): void {
    test('implements interface', function (): void {
        $collection = new Usersets([]);

        expect($collection)->toBeInstanceOf(UsersetsInterface::class);
    });

    test('creates empty', function (): void {
        $collection = new Usersets([]);

        expect($collection->count())->toBe(0);
        expect($collection->isEmpty())->toBeTrue();
    });

    test('creates with array of usersets', function (): void {
        $collection = new Usersets([
            new Userset(direct: new stdClass),
            new Userset(computedUserset: new ObjectRelation(relation: 'viewer')),
            new Userset(computedUserset: new ObjectRelation(relation: 'editor')),
        ]);

        expect($collection->count())->toBe(3);
        expect($collection->isEmpty())->toBeFalse();
    });

    test('adds usersets', function (): void {
        $collection = new Usersets([]);

        $userset = new Userset(direct: new stdClass);

        $collection->add($userset);

        expect($collection->count())->toBe(1);
        expect($collection->get(0))->toBe($userset);
    });

    test('gets usersets by index', function (): void {
        $userset1 = new Userset(direct: new stdClass);
        $userset2 = new Userset(computedUserset: new ObjectRelation(relation: 'owner'));

        $collection = new Usersets([
            $userset1,
            $userset2,
        ]);

        expect($collection->get(0))->toBe($userset1);
        expect($collection->get(1))->toBe($userset2);
        expect($collection->get(2))->toBeNull();
    });

    test('checks if userset exists by index', function (): void {
        $userset = new Userset(direct: new stdClass);

        $collection = new Usersets([
            $userset,
        ]);

        expect(isset($collection[0]))->toBeTrue();
        expect(isset($collection[1]))->toBeFalse();
    });

    test('iterates over usersets', function (): void {
        $collection = new Usersets([
            new Userset(direct: new stdClass),
            new Userset(computedUserset: new ObjectRelation(relation: 'viewer')),
            new Userset(computedUserset: new ObjectRelation(relation: 'editor')),
        ]);

        $keys = [];

        foreach ($collection as $key => $userset) {
            $keys[] = $key;
            expect($userset)->toBeInstanceOf(Userset::class);
        }

        expect($keys)->toBe([0, 1, 2]);
    });

    test('toArray', function (): void {
        $userset1 = new Userset(direct: new stdClass);
        $userset2 = new Userset(computedUserset: new ObjectRelation(relation: 'viewer'));

        $collection = new Usersets([
            $userset1,
            $userset2,
        ]);

        $array = $collection->toArray();

        expect($array)->toBeArray();
        expect($array)->toHaveCount(2);
        expect($array[0])->toBe($userset1);
        expect($array[1])->toBe($userset2);
    });

    test('jsonSerialize', function (): void {
        $collection = new Usersets([
            new Userset(direct: new stdClass),
            new Userset(computedUserset: new ObjectRelation(relation: 'viewer')),
            new Userset(
                difference: new DifferenceV1(
                    base: new Userset(computedUserset: new ObjectRelation(relation: 'editor')),
                    subtract: new Userset(computedUserset: new ObjectRelation(relation: 'blocked')),
                ),
            ),
        ]);

        $json = $collection->jsonSerialize();

        expect($json)->toBeArray();
        expect($json)->toHaveKey('child');
        expect($json['child'])->toBeArray();
        expect($json['child'])->toHaveCount(3);

        // Check direct userset
        expect($json['child'][0])->toHaveKey('this');
        expect($json['child'][0]['this'])->toBeInstanceOf(stdClass::class);

        // Check viewer userset
        expect($json['child'][1])->toHaveKey('computedUserset');
        expect($json['child'][1]['computedUserset'])->toBe(['relation' => 'viewer']);

        // Check difference userset
        expect($json['child'][2])->toHaveKey('difference');
        expect($json['child'][2]['difference'])->toHaveKey('base');
        expect($json['child'][2]['difference'])->toHaveKey('subtract');
    });

    test('handles different userset types', function (): void {
        $collection = new Usersets([
            // Direct assignment
            new Userset(direct: new stdClass),

            // Computed userset
            new Userset(computedUserset: new ObjectRelation(relation: 'viewer')),

            // Union of usersets
            new Userset(
                union: new Usersets([
                    new Userset(direct: new stdClass),
                    new Userset(computedUserset: new ObjectRelation(relation: 'viewer')),
                ]),
            ),

            // Intersection of usersets
            new Userset(
                intersection: new Usersets([
                    new Userset(computedUserset: new ObjectRelation(relation: 'editor')),
                    new Userset(computedUserset: new ObjectRelation(relation: 'active')),
                ]),
            ),

            // Difference of usersets
            new Userset(
                difference: new DifferenceV1(
                    base: new Userset(computedUserset: new ObjectRelation(relation: 'member')),
                    subtract: new Userset(computedUserset: new ObjectRelation(relation: 'banned')),
                ),
            ),

            // Tuple to userset
            new Userset(
                tupleToUserset: new TupleToUsersetV1(
                    tupleset: new ObjectRelation(relation: 'parent'),
                    computedUserset: new ObjectRelation(relation: 'owner'),
                ),
            ),
        ]);

        expect($collection->count())->toBe(6);

        // Verify we can access each by index
        expect($collection->get(0))->toBeInstanceOf(Userset::class);
        expect($collection->get(1))->toBeInstanceOf(Userset::class);
        expect($collection->get(2))->toBeInstanceOf(Userset::class);
        expect($collection->get(3))->toBeInstanceOf(Userset::class);
        expect($collection->get(4))->toBeInstanceOf(Userset::class);
        expect($collection->get(5))->toBeInstanceOf(Userset::class);
    });

    test('schema', function (): void {
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
        expect($collection->jsonSerialize())->toBe(['child' => []]);

        // Test iteration on empty collection
        $count = 0;

        foreach ($collection as $_) {
            ++$count;
        }
        expect($count)->toBe(0);

        // Test get on empty collection
        expect($collection->get(0))->toBeNull();
    });
});
