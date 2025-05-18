<?php

declare(strict_types=1);

use OpenFGA\Models\{UsersList, UsersListUser};

test('can create empty collection', function (): void {
    $usersList = new UsersList();

    expect($usersList)->toHaveCount(0);
});

test('can create collection with user items', function (): void {
    $user1 = new UsersListUser('user:1');
    $user2 = new UsersListUser('document:1#writer');

    $usersList = new UsersList($user1, $user2);

    expect($usersList)->toHaveCount(2)
        ->and($usersList[0])->toBe($user1)
        ->and($usersList[1])->toBe($user2);
});

test('can create collection with iterable', function (): void {
    $user1 = new UsersListUser('user:1');
    $user2 = new UsersListUser('document:1#writer');
    $userArray = [$user1, $user2];

    $usersList = new UsersList($userArray);

    expect($usersList)->toHaveCount(2)
        ->and($usersList[0])->toBe($user1)
        ->and($usersList[1])->toBe($user2);
});

test('can iterate over collection', function (): void {
    $user1 = new UsersListUser('user:1');
    $user2 = new UsersListUser('document:1#writer');
    $usersList = new UsersList($user1, $user2);

    $items = [];
    foreach ($usersList as $user) {
        $items[] = $user;
    }

    expect($items)->toHaveCount(2)
        ->and($items[0])->toBe($user1)
        ->and($items[1])->toBe($user2);
});

test('json serialize returns array of user strings', function (): void {
    $user1 = new UsersListUser('user:1');
    $user2 = new UsersListUser('document:1#writer');
    $usersList = new UsersList($user1, $user2);

    $result = $usersList->jsonSerialize();

    expect($result)->toBeArray()
        ->toHaveCount(2)
        ->and($result[0])->toBe('user:1')
        ->and($result[1])->toBe('document:1#writer');
});

test('can add users to collection', function (): void {
    $user1 = new UsersListUser('user:1');
    $user2 = new UsersListUser('document:1#writer');

    $usersList = new UsersList();
    $usersList[] = $user1;
    $usersList[] = $user2;

    expect($usersList)->toHaveCount(2)
        ->and($usersList[0])->toBe($user1)
        ->and($usersList[1])->toBe($user2);
});

test('can check if offset exists', function (): void {
    $user = new UsersListUser('user:1');
    $usersList = new UsersList($user);

    expect(isset($usersList[0]))->toBeTrue()
        ->and(isset($usersList[1]))->toBeFalse();
});

test('can unset offset', function (): void {
    $user1 = new UsersListUser('user:1');
    $user2 = new UsersListUser('document:1#writer');
    $usersList = new UsersList($user1, $user2);

    unset($usersList[0]);

    expect($usersList)->toHaveCount(1)
        ->and($usersList[0])->toBe($user2);
});

test('schema returns correct schema', function (): void {
    $schema = UsersList::schema();

    expect($schema->getItemType())->toBe(UsersListUser::class);
});
