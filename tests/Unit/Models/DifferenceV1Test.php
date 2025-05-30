<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models;

use OpenFGA\Models\Collections\Usersets;
use OpenFGA\Models\{DifferenceV1, DifferenceV1Interface, ObjectRelation, Userset};
use OpenFGA\Schema\SchemaInterface;
use stdClass;

describe('DifferenceV1 Model', function (): void {
    test('implements DifferenceV1Interface', function (): void {
        $base = new Userset(direct: new stdClass());
        $subtract = new Userset(direct: new stdClass());
        $difference = new DifferenceV1(base: $base, subtract: $subtract);

        expect($difference)->toBeInstanceOf(DifferenceV1Interface::class);
    });

    test('constructs with base and subtract usersets', function (): void {
        $base = new Userset(direct: new stdClass());
        $subtract = new Userset(
            computedUserset: new ObjectRelation(relation: 'blocked'),
        );

        $difference = new DifferenceV1(base: $base, subtract: $subtract);

        expect($difference->getBase())->toBe($base);
        expect($difference->getSubtract())->toBe($subtract);
    });

    test('serializes to JSON', function (): void {
        $base = new Userset(direct: new stdClass());
        $subtract = new Userset(
            computedUserset: new ObjectRelation(relation: 'blocked'),
        );

        $difference = new DifferenceV1(base: $base, subtract: $subtract);
        $json = $difference->jsonSerialize();

        expect($json)->toHaveKey('base');
        expect($json)->toHaveKey('subtract');

        // Check base has 'this' (not 'direct')
        expect($json['base'])->toHaveKey('this');
        expect($json['base']['this'])->toBeInstanceOf(stdClass::class);

        expect($json['subtract'])->toBe([
            'computedUserset' => ['relation' => 'blocked'],
        ]);
    });

    test('handles complex nested differences', function (): void {
        // Base: users who are owners OR editors
        $ownerUserset = new Userset(
            computedUserset: new ObjectRelation(relation: 'owner'),
        );
        $editorUserset = new Userset(
            computedUserset: new ObjectRelation(relation: 'editor'),
        );
        $base = new Userset(
            union: new Usersets([$ownerUserset, $editorUserset]),
        );

        // Subtract: blocked users
        $subtract = new Userset(
            computedUserset: new ObjectRelation(relation: 'blocked'),
        );

        $difference = new DifferenceV1(base: $base, subtract: $subtract);
        $json = $difference->jsonSerialize();

        expect($json['base'])->toHaveKey('union');
        expect($json['base']['union'])->toHaveKey('child');
        expect($json['base']['union']['child'])->toHaveCount(2);
        expect($json['subtract'])->toBe([
            'computedUserset' => ['relation' => 'blocked'],
        ]);
    });

    test('returns schema instance', function (): void {
        $schema = DifferenceV1::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema->getClassName())->toBe(DifferenceV1::class);

        $properties = $schema->getProperties();
        expect($properties)->toHaveCount(2);

        $propertyNames = array_keys($properties);
        expect($propertyNames)->toBe(['base', 'subtract']);
    });

    test('schema properties have correct configuration', function (): void {
        $schema = DifferenceV1::schema();
        $properties = $schema->getProperties();

        // Base property
        $baseProp = $properties['base'];
        expect($baseProp->name)->toBe('base');
        expect($baseProp->type)->toBe('object');
        expect($baseProp->required)->toBe(true);

        // Subtract property
        $subtractProp = $properties['subtract'];
        expect($subtractProp->name)->toBe('subtract');
        expect($subtractProp->type)->toBe('object');
        expect($subtractProp->required)->toBe(true);
    });

    test('schema is cached', function (): void {
        $schema1 = DifferenceV1::schema();
        $schema2 = DifferenceV1::schema();

        expect($schema1)->toBe($schema2);
    });

    test('handles typical authorization patterns', function (): void {
        // Pattern 1: All users except blocked ones
        $allUsers = new Userset(direct: new stdClass());
        $blockedUsers = new Userset(
            computedUserset: new ObjectRelation(relation: 'blocked'),
        );
        $activeUsers = new DifferenceV1(base: $allUsers, subtract: $blockedUsers);

        $json = $activeUsers->jsonSerialize();
        expect($json['base'])->toHaveKey('this');
        expect($json['subtract']['computedUserset']['relation'])->toBe('blocked');

        // Pattern 2: Editors except those in specific group
        $editors = new Userset(
            computedUserset: new ObjectRelation(relation: 'editor'),
        );
        $restrictedGroup = new Userset(
            computedUserset: new ObjectRelation(relation: 'restricted_editors'),
        );
        $allowedEditors = new DifferenceV1(base: $editors, subtract: $restrictedGroup);

        $json2 = $allowedEditors->jsonSerialize();
        expect($json2['base']['computedUserset']['relation'])->toBe('editor');
        expect($json2['subtract']['computedUserset']['relation'])->toBe('restricted_editors');
    });
});
