<?php

declare(strict_types=1);

use OpenFGA\Models\Collections\{UsersetUnion, Usersets};
use OpenFGA\Models\{ObjectRelation, Userset};

describe('UsersetUnion Collection', function (): void {
    test('can be instantiated', function (): void {
        $union = new UsersetUnion();

        expect($union)->toBeInstanceOf(UsersetUnion::class);
        expect($union->count())->toBe(0);
        expect($union->isEmpty())->toBeTrue();
    });

    test('only accepts UsersetInterface implementations', function (): void {
        $union = new UsersetUnion();

        // Create a valid Userset
        $userset = new Userset(
            computedUserset: new ObjectRelation(relation: 'viewer'),
        );

        $union->add($userset);

        expect($union->count())->toBe(1);
        expect($union->get(0))->toBe($userset);
    });

    test('throws TypeError when adding non-UsersetInterface item', function (): void {
        $union = new UsersetUnion();
        $invalidItem = new stdClass();

        expect(fn () => $union->add($invalidItem))
            ->toThrow(TypeError::class);
    });

    test('jsonSerialize returns empty child array for empty collection', function (): void {
        $union = new UsersetUnion();

        $serialized = $union->jsonSerialize();

        expect($serialized)->toBe(['child' => []]);
    });

    test('jsonSerialize wraps parent serialization under child key', function (): void {
        $union = new UsersetUnion();

        // Add a simple userset
        $userset1 = new Userset(
            computedUserset: new ObjectRelation(relation: 'reader'),
        );
        $union->add($userset1);

        $serialized = $union->jsonSerialize();

        expect($serialized)->toHaveKey('child');
        expect($serialized['child'])->toBeArray();
        expect($serialized['child'])->toHaveCount(1);
        expect($serialized['child'][0])->toBe($userset1->jsonSerialize());
    });

    test('jsonSerialize with multiple Userset instances', function (): void {
        $union = new UsersetUnion();

        // Add multiple usersets with different configurations
        $userset1 = new Userset(
            computedUserset: new ObjectRelation(relation: 'viewer'),
        );

        $userset2 = new Userset(
            computedUserset: new ObjectRelation(relation: 'editor'),
        );

        $userset3 = new Userset(
            direct: new stdClass(),
        );

        $union->add($userset1);
        $union->add($userset2);
        $union->add($userset3);

        $serialized = $union->jsonSerialize();

        // Verify structure
        expect($serialized)->toHaveKey('child');
        expect($serialized['child'])->toBeArray();
        expect($serialized['child'])->toHaveCount(3);

        // Verify each item structure matches (not exact object identity)
        expect($serialized['child'][0])->toEqual($userset1->jsonSerialize());
        expect($serialized['child'][1])->toEqual($userset2->jsonSerialize());
        expect($serialized['child'][2])->toEqual($userset3->jsonSerialize());
    });

    test('jsonSerialize exact structure for complex usersets', function (): void {
        $union = new UsersetUnion();

        // Create a userset with computedUserset (ObjectRelation filters out empty strings)
        $userset1 = new Userset(
            computedUserset: new ObjectRelation(relation: 'owner'),
        );

        // Create a userset with direct
        $direct = new stdClass();
        $userset2 = new Userset(direct: $direct);

        $union->add($userset1);
        $union->add($userset2);

        $serialized = $union->jsonSerialize();

        // Verify exact structure
        expect($serialized)->toHaveKey('child');
        expect($serialized['child'])->toHaveCount(2);

        // First item should have computedUserset with just relation (object filtered out if empty)
        expect($serialized['child'][0])->toEqual([
            'computedUserset' => [
                'relation' => 'owner',
            ],
        ]);

        // Second item should have 'this' with stdClass
        expect($serialized['child'][1])->toHaveKey('this');
        expect($serialized['child'][1]['this'])->toBeInstanceOf(stdClass::class);
    });

    test('can iterate over usersets', function (): void {
        $union = new UsersetUnion();

        $usersets = [];
        for ($i = 0; $i < 3; ++$i) {
            $userset = new Userset(
                computedUserset: new ObjectRelation(relation: "relation{$i}"),
            );
            $usersets[] = $userset;
            $union->add($userset);
        }

        $index = 0;
        foreach ($union as $key => $userset) {
            expect($key)->toBe($index);
            expect($userset)->toBe($usersets[$index]);
            ++$index;
        }

        expect($index)->toBe(3);
    });

    test('can be constructed with initial items', function (): void {
        $userset1 = new Userset(
            computedUserset: new ObjectRelation(relation: 'viewer'),
        );

        $userset2 = new Userset(
            computedUserset: new ObjectRelation(relation: 'editor'),
        );

        $union = new UsersetUnion($userset1, $userset2);

        expect($union->count())->toBe(2);
        expect($union->get(0))->toBe($userset1);
        expect($union->get(1))->toBe($userset2);
    });

    test('preserves userset integrity through serialization', function (): void {
        $union = new UsersetUnion();

        // Create a nested structure with Usersets (not UsersetUnion)
        $nestedUsersets = new Usersets();
        $nestedUsersets->add(new Userset(
            computedUserset: new ObjectRelation(relation: 'nested_viewer'),
        ));

        $userset = new Userset(union: $nestedUsersets);
        $union->add($userset);

        $serialized = $union->jsonSerialize();

        expect($serialized)->toHaveKey('child');
        expect($serialized['child'])->toHaveCount(1);
        expect($serialized['child'][0])->toHaveKey('union');
        expect($serialized['child'][0]['union'])->toHaveKey('child');
    });

    test('implements collection interface methods', function (): void {
        $union = new UsersetUnion();

        // Test isEmpty
        expect($union->isEmpty())->toBeTrue();

        $userset = new Userset(
            computedUserset: new ObjectRelation(relation: 'viewer'),
        );
        $union->add($userset);

        // Test isEmpty after adding
        expect($union->isEmpty())->toBeFalse();

        // Test toArray
        $array = $union->toArray();
        expect($array)->toBeArray();
        expect($array)->toHaveCount(1);
        expect($array[0])->toBe($userset);

        // Test ArrayAccess
        expect($union[0])->toBe($userset);
        expect(isset($union[0]))->toBeTrue();
        expect(isset($union[1]))->toBeFalse();
    });
});
