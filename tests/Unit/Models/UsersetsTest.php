<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models;

use InvalidArgumentException;
use OpenFGA\Models\{
    DirectUserset,
    DirectUsersetInterface,
    DifferenceV1,
    DifferenceV1Interface,
    ObjectRelation,
    ObjectRelationInterface,
    TupleToUsersetV1,
    TupleToUsersetV1Interface,
    Userset,
    UsersetInterface,
    Usersets,
    UsersetsInterface,
};

it('can create an ObjectRelation instance with constructor', function () {
    $object = 'document';
    $relation = 'viewer';

    $objectRelation = new ObjectRelation($object, $relation);

    expect($objectRelation)->toBeInstanceOf(ObjectRelationInterface::class)
        ->and($objectRelation->object)->toBe($object)
        ->and($objectRelation->relation)->toBe($relation);
});

it('can create an ObjectRelation instance with null values', function () {
    $objectRelation = new ObjectRelation();

    expect($objectRelation)->toBeInstanceOf(ObjectRelationInterface::class)
        ->and($objectRelation->object)->toBeNull()
        ->and($objectRelation->relation)->toBeNull();
});

it('can convert ObjectRelation to array', function () {
    $object = 'document';
    $relation = 'viewer';

    $objectRelation = new ObjectRelation($object, $relation);
    $array = $objectRelation->toArray();

    expect($array)->toBeArray()
        ->and($array['object'])->toBe($object)
        ->and($array['relation'])->toBe($relation);
});

it('can create ObjectRelation from array', function () {
    $data = [
        'object' => 'document',
        'relation' => 'viewer'
    ];

    $objectRelation = ObjectRelation::fromArray($data);

    expect($objectRelation)->toBeInstanceOf(ObjectRelationInterface::class)
        ->and($objectRelation->object)->toBe($data['object'])
        ->and($objectRelation->relation)->toBe($data['relation']);
});

it('can create a DirectUserset instance', function () {
    $directUserset = new DirectUserset();

    expect($directUserset)->toBeInstanceOf(DirectUsersetInterface::class);
});

it('can convert DirectUserset to array', function () {
    $directUserset = new DirectUserset();
    $array = $directUserset->toArray();

    expect($array)->toBeArray()
        ->and($array)->toBe([]);
});

it('can create DirectUserset from array', function () {
    $directUserset = DirectUserset::fromArray([]);

    expect($directUserset)->toBeInstanceOf(DirectUsersetInterface::class);
});

it('can create a TupleToUsersetV1 instance with constructor', function () {
    $tupleset = new ObjectRelation('document', 'parent');
    $computedUserset = new ObjectRelation('document', 'viewer');

    $tupleToUserset = new TupleToUsersetV1($tupleset, $computedUserset);

    expect($tupleToUserset)->toBeInstanceOf(TupleToUsersetV1Interface::class)
        ->and($tupleToUserset->tupleset)->toBe($tupleset)
        ->and($tupleToUserset->computedUserset)->toBe($computedUserset);
});

it('can convert TupleToUsersetV1 to array', function () {
    $tupleset = new ObjectRelation('document', 'parent');
    $computedUserset = new ObjectRelation('document', 'viewer');

    $tupleToUserset = new TupleToUsersetV1($tupleset, $computedUserset);
    $array = $tupleToUserset->toArray();

    expect($array)->toBeArray()
        ->and($array['tupleset'])->toEqual($tupleset->toArray())
        ->and($array['computed_userset'])->toEqual($computedUserset->toArray());
});

it('can create TupleToUsersetV1 from array', function () {
    $data = [
        'tupleset' => [
            'object' => 'document',
            'relation' => 'parent'
        ],
        'computed_userset' => [
            'object' => 'document',
            'relation' => 'viewer'
        ]
    ];

    $tupleToUserset = TupleToUsersetV1::fromArray($data);

    expect($tupleToUserset)->toBeInstanceOf(TupleToUsersetV1Interface::class)
        ->and($tupleToUserset->tupleset)->toBeInstanceOf(ObjectRelationInterface::class)
        ->and($tupleToUserset->tupleset->object)->toBe($data['tupleset']['object'])
        ->and($tupleToUserset->tupleset->relation)->toBe($data['tupleset']['relation'])
        ->and($tupleToUserset->computedUserset)->toBeInstanceOf(ObjectRelationInterface::class)
        ->and($tupleToUserset->computedUserset->object)->toBe($data['computed_userset']['object'])
        ->and($tupleToUserset->computedUserset->relation)->toBe($data['computed_userset']['relation']);
});

it('can create a DifferenceV1 instance with constructor', function () {
    $base = new Userset();
    $subtract = new Userset();

    $difference = new DifferenceV1($base, $subtract);

    expect($difference)->toBeInstanceOf(DifferenceV1Interface::class)
        ->and($difference->base)->toBe($base)
        ->and($difference->subtract)->toBe($subtract);
});

it('can convert DifferenceV1 to array', function () {
    $base = new Userset();
    $subtract = new Userset();

    $difference = new DifferenceV1($base, $subtract);
    $array = $difference->toArray();

    expect($array)->toBeArray()
        ->and($array['base'])->toEqual($base->toArray())
        ->and($array['subtract'])->toEqual($subtract->toArray());
});

it('throws exception when creating DifferenceV1 from array with missing base', function () {
    $data = [
        'subtract' => []
    ];

    DifferenceV1::fromArray($data);
})->throws(InvalidArgumentException::class, 'Missing base');

it('throws exception when creating DifferenceV1 from array with missing subtract', function () {
    $data = [
        'base' => []
    ];

    DifferenceV1::fromArray($data);
})->throws(InvalidArgumentException::class, 'Missing subtract');

it('can create a Userset instance with constructor', function () {
    $direct = new DirectUserset();
    $computedUserset = new ObjectRelation('document', 'viewer');

    $userset = new Userset($direct, $computedUserset);

    expect($userset)->toBeInstanceOf(UsersetInterface::class)
        ->and($userset->direct)->toBe($direct)
        ->and($userset->computedUserset)->toBe($computedUserset);
});

it('can create a Userset instance with null values', function () {
    $userset = new Userset();

    expect($userset)->toBeInstanceOf(UsersetInterface::class)
        ->and($userset->direct)->toBeNull()
        ->and($userset->computedUserset)->toBeNull()
        ->and($userset->tupleToUserset)->toBeNull()
        ->and($userset->union)->toBeNull()
        ->and($userset->intersection)->toBeNull()
        ->and($userset->difference)->toBeNull();
});

it('can convert Userset to array', function () {
    $direct = new DirectUserset();
    $computedUserset = new ObjectRelation('document', 'viewer');

    $userset = new Userset($direct, $computedUserset);
    $array = $userset->toArray();

    expect($array)->toBeArray()
        ->and($array['direct'])->toBe($direct->toArray())
        ->and($array['computed_userset'])->toBe($computedUserset->toArray())
        ->and($array['tuple_to_userset'])->toBeNull()
        ->and($array['union'])->toBeNull()
        ->and($array['intersection'])->toBeNull()
        ->and($array['difference'])->toBeNull();
});

it('can create an empty Usersets collection', function () {
    $usersets = new Usersets();

    expect($usersets)->toBeInstanceOf(UsersetsInterface::class)
        ->and($usersets)->toHaveCount(0);
});

it('can add Userset to Usersets collection', function () {
    $userset1 = new Userset();
    $userset2 = new Userset();

    $usersets = new Usersets();
    $usersets->add($userset1);
    $usersets->add($userset2);

    expect($usersets)->toHaveCount(2);
});

it('can get current Userset from Usersets collection', function () {
    $userset = new Userset();

    $usersets = new Usersets();
    $usersets->add($userset);

    expect($usersets->current())->toBe($userset);
});

it('can get Userset by offset from Usersets collection', function () {
    $userset1 = new Userset();
    $userset2 = new Userset();

    $usersets = new Usersets();
    $usersets->add($userset1);
    $usersets->add($userset2);

    expect($usersets->offsetGet(0))->toBe($userset1)
        ->and($usersets->offsetGet(1))->toBe($userset2)
        ->and($usersets->offsetGet(2))->toBeNull();
});

it('can create Usersets collection from array', function () {
    $data = [
        [],
        []
    ];

    $usersets = Usersets::fromArray($data);

    expect($usersets)->toBeInstanceOf(UsersetsInterface::class)
        ->and($usersets)->toHaveCount(2)
        ->and($usersets->offsetGet(0))->toBeInstanceOf(Userset::class)
        ->and($usersets->offsetGet(1))->toBeInstanceOf(Userset::class);
});
