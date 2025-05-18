<?php

declare(strict_types=1);

use OpenFGA\Models\{ObjectRelation, Userset, Usersets};

test('can create empty collection', function (): void {
    $usersets = new Usersets();

    expect($usersets)->toHaveCount(0);
});

test('can create collection with userset items', function (): void {
    $userset1 = new Userset(direct: (object) ['type' => 'user', 'id' => '1']);
    $userset2 = new Userset(computedUserset: new ObjectRelation('document', 'reader'));

    $usersets = new Usersets($userset1, $userset2);

    expect($usersets)->toHaveCount(2)
        ->and($usersets[0])->toBe($userset1)
        ->and($usersets[1])->toBe($userset2);
});

test('can create collection with iterable', function (): void {
    $userset1 = new Userset(direct: (object) ['type' => 'user', 'id' => '1']);
    $userset2 = new Userset(computedUserset: new ObjectRelation('document', 'reader'));
    $usersetArray = [$userset1, $userset2];

    $usersets = new Usersets($usersetArray);

    expect($usersets)->toHaveCount(2)
        ->and($usersets[0])->toBe($userset1)
        ->and($usersets[1])->toBe($userset2);
});

test('can iterate over collection', function (): void {
    $userset1 = new Userset(direct: (object) ['type' => 'user', 'id' => '1']);
    $userset2 = new Userset(computedUserset: new ObjectRelation('document', 'reader'));
    $usersets = new Usersets($userset1, $userset2);

    $items = [];
    foreach ($usersets as $userset) {
        $items[] = $userset;
    }

    expect($items)->toHaveCount(2)
        ->and($items[0])->toBe($userset1)
        ->and($items[1])->toBe($userset2);
});

test('json serialize returns array of serialized usersets', function (): void {
    $userset1 = new Userset(direct: (object) ['type' => 'user', 'id' => '1']);
    $userset2 = new Userset(computedUserset: new ObjectRelation('document', 'reader'));
    $usersets = new Usersets($userset1, $userset2);

    $result = $usersets->jsonSerialize();

    expect($result)->toBeArray()
        ->toHaveCount(2)
        ->and($result[0])->toBe($userset1->jsonSerialize())
        ->and($result[1])->toBe($userset2->jsonSerialize());
});

test('can add usersets to collection', function (): void {
    $userset1 = new Userset(direct: (object) ['type' => 'user', 'id' => '1']);
    $userset2 = new Userset(computedUserset: new ObjectRelation('document', 'reader'));

    $usersets = new Usersets();
    $usersets[] = $userset1;
    $usersets[] = $userset2;

    expect($usersets)->toHaveCount(2)
        ->and($usersets[0])->toBe($userset1)
        ->and($usersets[1])->toBe($userset2);
});

test('can check if offset exists', function (): void {
    $userset = new Userset(direct: (object) ['type' => 'user', 'id' => '1']);
    $usersets = new Usersets($userset);

    expect(isset($usersets[0]))->toBeTrue()
        ->and(isset($usersets[1]))->toBeFalse();
});

test('can unset offset', function (): void {
    $userset1 = new Userset(direct: (object) ['type' => 'user', 'id' => '1']);
    $userset2 = new Userset(computedUserset: new ObjectRelation('document', 'reader'));
    $usersets = new Usersets($userset1, $userset2);

    unset($usersets[0]);

    expect($usersets)->toHaveCount(1)
        ->and(isset($usersets[0]))->toBeFalse()
        ->and($usersets[1])->toBe($userset2);
});

test('add method enforces Userset type', function (): void {
    $usersets = new Usersets();
    // Create a valid Userset object instead of stdClass
    $validUserset = new Userset(direct: (object) ['type' => 'user', 'id' => '1']);

    // This should not throw an exception
    $usersets->add($validUserset);

    expect($usersets)->toHaveCount(1);
});

test('schema returns correct schema', function (): void {
    $schema = Usersets::schema();

    expect($schema)->toBeInstanceOf(OpenFGA\Schema\CollectionSchemaInterface::class);
    expect($schema->getItemType())->toBe(Userset::class);
});
