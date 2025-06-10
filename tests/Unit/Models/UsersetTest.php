<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models;

use OpenFGA\Models\Collections\Usersets;
use OpenFGA\Models\{DifferenceV1, ObjectRelation, TupleToUsersetV1, Userset, UsersetInterface};
use OpenFGA\Schemas\SchemaInterface;
use stdClass;

describe('Userset Model', function (): void {
    test('implements UsersetInterface', function (): void {
        $userset = new Userset;

        expect($userset)->toBeInstanceOf(UsersetInterface::class);
    });

    test('constructs with all null parameters', function (): void {
        $userset = new Userset;

        expect($userset->getDirect())->toBeNull();
        expect($userset->getComputedUserset())->toBeNull();
        expect($userset->getTupleToUserset())->toBeNull();
        expect($userset->getUnion())->toBeNull();
        expect($userset->getIntersection())->toBeNull();
        expect($userset->getDifference())->toBeNull();
    });

    test('constructs with direct', function (): void {
        $direct = new stdClass;
        $userset = new Userset(direct: $direct);

        expect($userset->getDirect())->toBe($direct);
        expect($userset->getComputedUserset())->toBeNull();
        expect($userset->getTupleToUserset())->toBeNull();
        expect($userset->getUnion())->toBeNull();
        expect($userset->getIntersection())->toBeNull();
        expect($userset->getDifference())->toBeNull();
    });

    test('constructs with computed userset', function (): void {
        $computedUserset = new ObjectRelation(relation: 'viewer');
        $userset = new Userset(computedUserset: $computedUserset);

        expect($userset->getDirect())->toBeNull();
        expect($userset->getComputedUserset())->toBe($computedUserset);
        expect($userset->getTupleToUserset())->toBeNull();
        expect($userset->getUnion())->toBeNull();
        expect($userset->getIntersection())->toBeNull();
        expect($userset->getDifference())->toBeNull();
    });

    test('constructs with tuple to userset', function (): void {
        $tupleToUserset = new TupleToUsersetV1(
            tupleset: new ObjectRelation(relation: 'parent'),
            computedUserset: new ObjectRelation(relation: 'viewer'),
        );
        $userset = new Userset(tupleToUserset: $tupleToUserset);

        expect($userset->getDirect())->toBeNull();
        expect($userset->getComputedUserset())->toBeNull();
        expect($userset->getTupleToUserset())->toBe($tupleToUserset);
        expect($userset->getUnion())->toBeNull();
        expect($userset->getIntersection())->toBeNull();
        expect($userset->getDifference())->toBeNull();
    });

    test('constructs with union', function (): void {
        $child1 = new Userset(direct: new stdClass);
        $child2 = new Userset(computedUserset: new ObjectRelation(relation: 'viewer'));
        $union = new Usersets([$child1, $child2]);

        $userset = new Userset(union: $union);

        expect($userset->getDirect())->toBeNull();
        expect($userset->getComputedUserset())->toBeNull();
        expect($userset->getTupleToUserset())->toBeNull();
        expect($userset->getUnion())->toBe($union);
        expect($userset->getIntersection())->toBeNull();
        expect($userset->getDifference())->toBeNull();
    });

    test('constructs with intersection', function (): void {
        $child1 = new Userset(direct: new stdClass);
        $child2 = new Userset(computedUserset: new ObjectRelation(relation: 'viewer'));
        $intersection = new Usersets([$child1, $child2]);

        $userset = new Userset(intersection: $intersection);

        expect($userset->getDirect())->toBeNull();
        expect($userset->getComputedUserset())->toBeNull();
        expect($userset->getTupleToUserset())->toBeNull();
        expect($userset->getUnion())->toBeNull();
        expect($userset->getIntersection())->toBe($intersection);
        expect($userset->getDifference())->toBeNull();
    });

    test('constructs with difference', function (): void {
        $base = new Userset(direct: new stdClass);
        $subtract = new Userset(computedUserset: new ObjectRelation(relation: 'blocked'));
        $difference = new DifferenceV1(base: $base, subtract: $subtract);

        $userset = new Userset(difference: $difference);

        expect($userset->getDirect())->toBeNull();
        expect($userset->getComputedUserset())->toBeNull();
        expect($userset->getTupleToUserset())->toBeNull();
        expect($userset->getUnion())->toBeNull();
        expect($userset->getIntersection())->toBeNull();
        expect($userset->getDifference())->toBe($difference);
    });

    test('serializes to JSON with only non-null fields', function (): void {
        $userset = new Userset;
        expect($userset->jsonSerialize())->toBe([]);

        $userset = new Userset(direct: new stdClass);
        $json = $userset->jsonSerialize();
        expect($json)->toHaveKey('this');
        expect($json['this'])->toBeInstanceOf(stdClass::class);

        $computedUserset = new ObjectRelation(relation: 'viewer');
        $userset = new Userset(computedUserset: $computedUserset);
        expect($userset->jsonSerialize())->toBe([
            'computedUserset' => ['relation' => 'viewer'],
        ]);
    });

    test('serializes complex nested userset', function (): void {
        $child1 = new Userset(direct: new stdClass);
        $child2 = new Userset(
            computedUserset: new ObjectRelation(relation: 'viewer'),
        );
        $union = new Usersets([$child1, $child2]);

        $userset = new Userset(union: $union);

        $json = $userset->jsonSerialize();
        expect($json)->toHaveKey('union');
        expect($json['union'])->toHaveKey('child');
        expect($json['union']['child'])->toHaveCount(2);

        expect($json['union']['child'][0])->toHaveKey('this');
        expect($json['union']['child'][0]['this'])->toBeInstanceOf(stdClass::class);

        expect($json['union']['child'][1])->toHaveKey('computedUserset');
        expect($json['union']['child'][1]['computedUserset'])->toBe(['relation' => 'viewer']);
    });

    test('returns schema instance', function (): void {
        $schema = Userset::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema->getClassName())->toBe(Userset::class);

        $properties = $schema->getProperties();
        expect($properties)->toHaveCount(6);

        $propertyNames = array_keys($properties);
        expect($propertyNames)->toBe([
            'this',
            'computedUserset',
            'tupleToUserset',
            'union',
            'intersection',
            'difference',
        ]);
    });

    test('schema properties have correct configuration', function (): void {
        $schema = Userset::schema();
        $properties = $schema->getProperties();

        $thisProp = $properties['this'];
        expect($thisProp->name)->toBe('this');
        expect($thisProp->type)->toBe('object');
        expect($thisProp->required)->toBe(false);

        $computedUsersetProp = $properties['computedUserset'];
        expect($computedUsersetProp->name)->toBe('computedUserset');
        expect($computedUsersetProp->type)->toBe('object');
        expect($computedUsersetProp->className)->toBe(ObjectRelation::class);
        expect($computedUsersetProp->required)->toBe(false);

        $tupleToUsersetProp = $properties['tupleToUserset'];
        expect($tupleToUsersetProp->name)->toBe('tupleToUserset');
        expect($tupleToUsersetProp->type)->toBe('object');
        expect($tupleToUsersetProp->className)->toBe(TupleToUsersetV1::class);
        expect($tupleToUsersetProp->required)->toBe(false);

        $unionProp = $properties['union'];
        expect($unionProp->name)->toBe('union');
        expect($unionProp->type)->toBe('object');
        expect($unionProp->className)->toBe(Usersets::class);
        expect($unionProp->required)->toBe(false);

        $intersectionProp = $properties['intersection'];
        expect($intersectionProp->name)->toBe('intersection');
        expect($intersectionProp->type)->toBe('object');
        expect($intersectionProp->className)->toBe(Usersets::class);
        expect($intersectionProp->required)->toBe(false);

        $differenceProp = $properties['difference'];
        expect($differenceProp->name)->toBe('difference');
        expect($differenceProp->type)->toBe('object');
        expect($differenceProp->className)->toBe(DifferenceV1::class);
        expect($differenceProp->required)->toBe(false);
    });

    test('schema is cached', function (): void {
        $schema1 = Userset::schema();
        $schema2 = Userset::schema();

        expect($schema1)->toBe($schema2);
    });

    test('only one field should be set at a time', function (): void {
        $userset1 = new Userset(direct: new stdClass);
        $userset2 = new Userset(computedUserset: new ObjectRelation(relation: 'viewer'));
        $userset3 = new Userset(union: new Usersets([]));

        expect($userset1->jsonSerialize())->toHaveCount(1);
        expect($userset2->jsonSerialize())->toHaveCount(1);
        expect($userset3->jsonSerialize())->toHaveCount(1);
    });

    test('handles typical authorization patterns', function (): void {
        $directUserset = new Userset(direct: new stdClass);
        $json = $directUserset->jsonSerialize();
        expect($json)->toHaveKey('this');
        expect($json['this'])->toBeInstanceOf(stdClass::class);

        $computedUserset = new Userset(
            computedUserset: new ObjectRelation(relation: 'owner'),
        );
        expect($computedUserset->jsonSerialize())->toBe([
            'computedUserset' => ['relation' => 'owner'],
        ]);

        $ownerUserset = new Userset(
            computedUserset: new ObjectRelation(relation: 'owner'),
        );
        $editorUserset = new Userset(
            computedUserset: new ObjectRelation(relation: 'editor'),
        );
        $viewerUserset = new Userset(
            union: new Usersets([$ownerUserset, $editorUserset]),
        );

        $json = $viewerUserset->jsonSerialize();
        expect($json)->toHaveKey('union');
        expect($json['union'])->toHaveKey('child');
        expect($json['union']['child'])->toHaveCount(2);
    });

    test('jsonSerialize includes tupleToUserset when present', function (): void {
        $tupleToUserset = new TupleToUsersetV1(
            tupleset: new ObjectRelation(relation: 'parent'),
            computedUserset: new ObjectRelation(relation: 'viewer'),
        );
        $userset = new Userset(tupleToUserset: $tupleToUserset);

        $json = $userset->jsonSerialize();

        expect($json)->toHaveKey('tupleToUserset');
        expect($json['tupleToUserset'])->toBe([
            'tupleset' => ['relation' => 'parent'],
            'computedUserset' => ['relation' => 'viewer'],
        ]);
        expect($json)->toHaveCount(1);
    });

    test('jsonSerialize includes intersection when present', function (): void {
        $child1 = new Userset(direct: new stdClass);
        $child2 = new Userset(computedUserset: new ObjectRelation(relation: 'member'));
        $intersection = new Usersets([$child1, $child2]);

        $userset = new Userset(intersection: $intersection);

        $json = $userset->jsonSerialize();

        expect($json)->toHaveKey('intersection');
        expect($json['intersection'])->toHaveKey('child');
        expect($json['intersection']['child'])->toHaveCount(2);

        expect($json['intersection']['child'][0])->toHaveKey('this');
        expect($json['intersection']['child'][0]['this'])->toBeInstanceOf(stdClass::class);

        expect($json['intersection']['child'][1])->toHaveKey('computedUserset');
        expect($json['intersection']['child'][1]['computedUserset'])->toBe(['relation' => 'member']);

        expect($json)->toHaveCount(1);
    });

    test('jsonSerialize includes union when present', function (): void {
        $child1 = new Userset(direct: new stdClass);
        $child2 = new Userset(computedUserset: new ObjectRelation(relation: 'admin'));
        $union = new Usersets([$child1, $child2]);

        $userset = new Userset(union: $union);

        $json = $userset->jsonSerialize();

        expect($json)->toHaveKey('union');
        expect($json['union'])->toHaveKey('child');
        expect($json['union']['child'])->toHaveCount(2);

        expect($json['union']['child'][0])->toHaveKey('this');
        expect($json['union']['child'][1])->toHaveKey('computedUserset');
        expect($json['union']['child'][1]['computedUserset'])->toBe(['relation' => 'admin']);

        expect($json)->toHaveCount(1);
    });

    test('jsonSerialize includes difference when present', function (): void {
        $base = new Userset(direct: new stdClass);
        $subtract = new Userset(computedUserset: new ObjectRelation(relation: 'blocked'));
        $difference = new DifferenceV1(base: $base, subtract: $subtract);

        $userset = new Userset(difference: $difference);

        $json = $userset->jsonSerialize();

        expect($json)->toHaveKey('difference');
        expect($json['difference'])->toHaveKey('base');
        expect($json['difference'])->toHaveKey('subtract');

        expect($json['difference']['base'])->toHaveKey('this');
        expect($json['difference']['base']['this'])->toBeInstanceOf(stdClass::class);

        expect($json['difference']['subtract'])->toHaveKey('computedUserset');
        expect($json['difference']['subtract']['computedUserset'])->toBe(['relation' => 'blocked']);

        expect($json)->toHaveCount(1);
    });
});
