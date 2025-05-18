<?php

declare(strict_types=1);

use OpenFGA\Models\{DifferenceV1, ObjectRelation, TupleToUsersetV1, Userset, Usersets};

test('constructor and getters', function (): void {
    $direct = (object) ['type' => 'user', 'id' => '123'];
    $computedUserset = new ObjectRelation('document:1', 'reader');
    $tupleToUserset = new TupleToUsersetV1(
        new ObjectRelation('document', 'reader'),
        new ObjectRelation('user', ''),
    );
    $union = new Usersets([new Userset()]);
    $intersection = new Usersets([new Userset()]);

    $baseUserset = new Userset(computedUserset: new ObjectRelation('document:1', 'reader'));
    $subtractUserset = new Userset(computedUserset: new ObjectRelation('user:1', ''));
    $difference = new DifferenceV1($baseUserset, $subtractUserset);

    $userset = new Userset(
        direct: $direct,
        computedUserset: $computedUserset,
        tupleToUserset: $tupleToUserset,
        union: $union,
        intersection: $intersection,
        difference: $difference,
    );

    expect($userset->getDirect())->toBe($direct)
        ->and($userset->getComputedUserset())->toBe($computedUserset)
        ->and($userset->getTupleToUserset())->toBe($tupleToUserset)
        ->and($userset->getUnion())->toBe($union)
        ->and($userset->getIntersection())->toBe($intersection)
        ->and($userset->getDifference())->toBe($difference);
});

test('json serialize with all properties', function (): void {
    $direct = (object) ['type' => 'user', 'id' => '123'];
    $computedUserset = new ObjectRelation('document:1', 'reader');
    $tupleToUserset = new TupleToUsersetV1(
        new ObjectRelation('document', 'reader'),
        new ObjectRelation('user', ''),
    );
    $union = new Usersets([new Userset()]);
    $intersection = new Usersets([new Userset()]);

    $baseUserset = new Userset(computedUserset: new ObjectRelation('document:1', 'reader'));
    $subtractUserset = new Userset(computedUserset: new ObjectRelation('user:1', ''));
    $difference = new DifferenceV1($baseUserset, $subtractUserset);

    $userset = new Userset(
        direct: $direct,
        computedUserset: $computedUserset,
        tupleToUserset: $tupleToUserset,
        union: $union,
        intersection: $intersection,
        difference: $difference,
    );

    $result = $userset->jsonSerialize();

    expect($result)->toMatchArray([
        'direct' => $direct,
        'computed_userset' => $computedUserset->jsonSerialize(),
        'tuple_to_userset' => $tupleToUserset->jsonSerialize(),
        'union' => $union->jsonSerialize(),
        'intersection' => $intersection->jsonSerialize(),
        'difference' => $difference->jsonSerialize(),
    ]);
});

test('json serialize with null properties', function (): void {
    $userset = new Userset();

    $result = $userset->jsonSerialize();

    expect($result)->toBeEmpty();
});

test('schema returns correct schema', function (): void {
    $schema = Userset::schema();

    $properties = $schema->getProperties();

    $propertyNames = array_keys($properties);
    expect($propertyNames)->toHaveCount(6)
        ->toContain('direct')
        ->toContain('computed_userset')
        ->toContain('tuple_to_userset')
        ->toContain('union')
        ->toContain('intersection')
        ->toContain('difference');
});
