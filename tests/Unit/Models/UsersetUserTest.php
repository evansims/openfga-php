<?php

declare(strict_types=1);

use OpenFGA\Models\UsersetUser;

test('constructor and getters', function (): void {
    $type = 'document';
    $id = '1';
    $relation = 'reader';

    $usersetUser = new UsersetUser($type, $id, $relation);

    expect($usersetUser->getType())->toBe($type)
        ->and($usersetUser->getId())->toBe($id)
        ->and($usersetUser->getRelation())->toBe($relation);
});

test('json serialize returns correct structure', function (): void {
    $type = 'document';
    $id = '1';
    $relation = 'reader';

    $usersetUser = new UsersetUser($type, $id, $relation);

    $result = $usersetUser->jsonSerialize();

    expect($result)->toMatchArray([
        'type' => $type,
        'id' => $id,
        'relation' => $relation,
    ]);
});

test('schema returns correct schema', function (): void {
    $schema = UsersetUser::schema();
    $properties = $schema->getProperties();

    expect($properties)->toHaveCount(3)
        ->and(\array_key_exists('type', $properties))->toBeTrue()
        ->and($properties['type']->name)->toBe('type')
        ->and($properties['type']->required)->toBeTrue()
        ->and(\array_key_exists('id', $properties))->toBeTrue()
        ->and($properties['id']->name)->toBe('id')
        ->and($properties['id']->required)->toBeTrue()
        ->and(\array_key_exists('relation', $properties))->toBeTrue()
        ->and($properties['relation']->name)->toBe('relation')
        ->and($properties['relation']->required)->toBeTrue();
});
