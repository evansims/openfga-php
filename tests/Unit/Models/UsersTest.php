<?php

declare(strict_types=1);

use OpenFGA\Models\{User, Users, UsersetUser};

test('can create empty collection', function (): void {
    $users = new Users();

    expect($users)->toHaveCount(0);
});

test('can create collection with user items', function (): void {
    $user1 = new User(object: (object) ['type' => 'user', 'id' => '1']);
    $user2 = new User(userset: new UsersetUser('document:1#writer'));

    $users = new Users($user1, $user2);

    expect($users)->toHaveCount(2)
        ->and($users[0])->toBe($user1)
        ->and($users[1])->toBe($user2);
});

test('can create collection with iterable', function (): void {
    $user1 = new User(object: (object) ['type' => 'user', 'id' => '1']);
    $user2 = new User(userset: new UsersetUser('document:1#writer'));
    $userArray = [$user1, $user2];

    $users = new Users($userArray);

    expect($users)->toHaveCount(2)
        ->and($users[0])->toBe($user1)
        ->and($users[1])->toBe($user2);
});

test('can iterate over collection', function (): void {
    $user1 = new User(object: (object) ['type' => 'user', 'id' => '1']);
    $user2 = new User(userset: new UsersetUser('document:1#writer'));
    $users = new Users($user1, $user2);

    $items = [];
    foreach ($users as $user) {
        $items[] = $user;
    }

    expect($items)->toHaveCount(2)
        ->and($items[0])->toBe($user1)
        ->and($items[1])->toBe($user2);
});

test('json serialize returns array of serialized users', function (): void {
    $user1 = new User(object: (object) ['type' => 'user', 'id' => '1']);
    $user2 = new User(userset: new UsersetUser('document:1#writer'));
    $users = new Users($user1, $user2);

    $result = $users->jsonSerialize();

    expect($result)->toBeArray()
        ->toHaveCount(2)
        ->and($result[0])->toBe($user1->jsonSerialize())
        ->and($result[1])->toBe($user2->jsonSerialize());
});

test('can add users to collection', function (): void {
    $user1 = new User(object: (object) ['type' => 'user', 'id' => '1']);
    $user2 = new User(userset: new UsersetUser('document:1#writer'));

    $users = new Users();
    $users[] = $user1;
    $users[] = $user2;

    expect($users)->toHaveCount(2)
        ->and($users[0])->toBe($user1)
        ->and($users[1])->toBe($user2);
});

test('can check if offset exists', function (): void {
    $user = new User(object: (object) ['type' => 'user', 'id' => '1']);
    $users = new Users($user);

    expect(isset($users[0]))->toBeTrue()
        ->and(isset($users[1]))->toBeFalse();
});

test('can unset offset', function (): void {
    $user1 = new User(object: (object) ['type' => 'user', 'id' => '1']);
    $user2 = new User(userset: new UsersetUser('document:1#writer'));
    $users = new Users($user1, $user2);

    unset($users[0]);

    expect($users)->toHaveCount(1)
        ->and($users[0])->toBe($user2);
});

test('schema returns correct schema', function (): void {
    $schema = Users::schema();

    expect($schema->getItemType())->toBe(User::class);
});
