<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models;

use OpenFGA\Models\{Computed, UsersetTreeTupleToUserset, UsersetTreeTupleToUsersetInterface};
use OpenFGA\Schemas\SchemaInterface;

describe('UsersetTreeTupleToUserset Model', function (): void {
    test('implements UsersetTreeTupleToUsersetInterface', function (): void {
        $computed = [
            new Computed(userset: 'viewer'),
        ];
        $tupleToUserset = new UsersetTreeTupleToUserset(
            tupleset: 'parent',
            computed: $computed,
        );

        expect($tupleToUserset)->toBeInstanceOf(UsersetTreeTupleToUsersetInterface::class);
    });

    test('constructs with tupleset and computed', function (): void {
        $computed = [
            new Computed(userset: 'viewer'),
            new Computed(userset: 'editor'),
        ];

        $tupleToUserset = new UsersetTreeTupleToUserset(
            tupleset: 'parent',
            computed: $computed,
        );

        expect($tupleToUserset->getTupleset())->toBe('parent');
        expect($tupleToUserset->getComputed())->toBe($computed);
    });

    test('serializes to JSON', function (): void {
        $computed = [
            new Computed(userset: 'viewer'),
        ];

        $tupleToUserset = new UsersetTreeTupleToUserset(
            tupleset: 'parent',
            computed: $computed,
        );

        expect($tupleToUserset->jsonSerialize())->toBe([
            'tupleset' => 'parent',
            'computed' => [
                ['userset' => 'viewer'],
            ],
        ]);
    });

    test('handles multiple computed usersets', function (): void {
        $computed = [
            new Computed(userset: 'viewer'),
            new Computed(userset: 'editor'),
            new Computed(userset: 'owner'),
        ];

        $tupleToUserset = new UsersetTreeTupleToUserset(
            tupleset: 'organization',
            computed: $computed,
        );

        $json = $tupleToUserset->jsonSerialize();
        expect($json['tupleset'])->toBe('organization');
        expect($json['computed'])->toHaveCount(3);
        expect($json['computed'][0])->toBe(['userset' => 'viewer']);
        expect($json['computed'][1])->toBe(['userset' => 'editor']);
        expect($json['computed'][2])->toBe(['userset' => 'owner']);
    });

    test('returns schema instance', function (): void {
        $schema = UsersetTreeTupleToUserset::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema->getClassName())->toBe(UsersetTreeTupleToUserset::class);

        $properties = $schema->getProperties();
        expect($properties)->toHaveCount(2);

        $propertyNames = array_keys($properties);
        expect($propertyNames)->toBe(['tupleset', 'computed']);
    });

    test('schema properties have correct configuration', function (): void {
        $schema = UsersetTreeTupleToUserset::schema();
        $properties = $schema->getProperties();

        // Tupleset property
        $tuplesetProp = $properties['tupleset'];
        expect($tuplesetProp->name)->toBe('tupleset');
        expect($tuplesetProp->type)->toBe('string');
        expect($tuplesetProp->required)->toBe(true);

        // Computed property
        $computedProp = $properties['computed'];
        expect($computedProp->name)->toBe('computed');
        expect($computedProp->type)->toBe('array');
        expect($computedProp->required)->toBe(true);
    });

    test('schema is cached', function (): void {
        $schema1 = UsersetTreeTupleToUserset::schema();
        $schema2 = UsersetTreeTupleToUserset::schema();

        expect($schema1)->toBe($schema2);
    });

    test('handles typical authorization patterns', function (): void {
        // Pattern 1: Viewers of parent folder can view documents
        $folderViewers = new UsersetTreeTupleToUserset(
            tupleset: 'parent',
            computed: [
                new Computed(userset: 'viewer'),
            ],
        );

        $json = $folderViewers->jsonSerialize();
        expect($json['tupleset'])->toBe('parent');
        expect($json['computed'][0]['userset'])->toBe('viewer');

        // Pattern 2: Organization members have multiple roles
        $orgMembers = new UsersetTreeTupleToUserset(
            tupleset: 'organization',
            computed: [
                new Computed(userset: 'member'),
                new Computed(userset: 'admin'),
                new Computed(userset: 'owner'),
            ],
        );

        $json2 = $orgMembers->jsonSerialize();
        expect($json2['tupleset'])->toBe('organization');
        expect($json2['computed'])->toHaveCount(3);

        // Pattern 3: Team hierarchy permissions
        $teamHierarchy = new UsersetTreeTupleToUserset(
            tupleset: 'team#parent',
            computed: [
                new Computed(userset: 'lead'),
                new Computed(userset: 'member'),
            ],
        );

        $json3 = $teamHierarchy->jsonSerialize();
        expect($json3['tupleset'])->toBe('team#parent');
        expect($json3['computed'])->toHaveCount(2);
    });

    test('handles complex tupleset paths', function (): void {
        $complexPath = new UsersetTreeTupleToUserset(
            tupleset: 'department#organization#parent',
            computed: [
                new Computed(userset: 'executive'),
            ],
        );

        expect($complexPath->getTupleset())->toBe('department#organization#parent');
        expect($complexPath->jsonSerialize()['tupleset'])->toBe('department#organization#parent');
    });
});
