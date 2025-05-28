<?php

declare(strict_types=1);

use OpenFGA\Models\Collections\Users;
use OpenFGA\Models\User;
use OpenFGA\Responses\{ListUsersResponse, ListUsersResponseInterface};
use OpenFGA\Schema\SchemaInterface;

test('ListUsersResponse implements ListUsersResponseInterface', function (): void {
    $users = new Users([]);
    $response = new ListUsersResponse($users);

    expect($response)->toBeInstanceOf(ListUsersResponseInterface::class);
});

test('ListUsersResponse constructs with users collection', function (): void {
    $object1 = (object) ['id' => 'user:alice'];
    $object2 = (object) ['id' => 'user:bob'];
    $user1 = new User(object: $object1);
    $user2 = new User(object: $object2);
    $users = new Users([$user1, $user2]);

    $response = new ListUsersResponse($users);

    expect($response->getUsers())->toBe($users);
    expect($response->getUsers())->toHaveCount(2);
});

test('ListUsersResponse constructs with empty users collection', function (): void {
    $users = new Users([]);
    $response = new ListUsersResponse($users);

    expect($response->getUsers())->toBe($users);
    expect($response->getUsers())->toHaveCount(0);
    expect($response->getUsers())->toBeInstanceOf(Users::class);
});

test('ListUsersResponse handles single user', function (): void {
    $object = (object) ['id' => 'user:alice'];
    $user = new User(object: $object);
    $users = new Users([$user]);
    $response = new ListUsersResponse($users);

    expect($response->getUsers())->toHaveCount(1);
    expect($response->getUsers()->first())->toBe($user);
});

test('ListUsersResponse handles multiple users', function (): void {
    $object1 = (object) ['id' => 'user:alice'];
    $object2 = (object) ['id' => 'user:bob'];
    $object3 = (object) ['id' => 'user:charlie'];
    $user1 = new User(object: $object1);
    $user2 = new User(object: $object2);
    $user3 = new User(object: $object3);
    $users = new Users([$user1, $user2, $user3]);

    $response = new ListUsersResponse($users);

    expect($response->getUsers())->toHaveCount(3);
    expect($response->getUsers()->toArray())->toBe([$user1, $user2, $user3]);
});

test('ListUsersResponse schema returns correct structure', function (): void {
    $schema = ListUsersResponse::schema();

    expect($schema)->toBeInstanceOf(SchemaInterface::class);
    expect($schema->getClassName())->toBe(ListUsersResponse::class);

    $properties = $schema->getProperties();
    expect($properties)->toHaveCount(1);
    expect($properties)->toHaveKey('users');

    expect($properties['users']->name)->toBe('users');
    expect($properties['users']->type)->toBe(Users::class);
    expect($properties['users']->required)->toBeTrue();
});

test('ListUsersResponse schema is cached', function (): void {
    $schema1 = ListUsersResponse::schema();
    $schema2 = ListUsersResponse::schema();

    expect($schema1)->toBe($schema2);
});

// Note: fromResponse method testing would require integration tests due to SchemaValidator complexity
// These tests focus on the model's direct functionality

test('ListUsersResponse handles empty users array data', function (): void {
    $users = new Users([]);
    $response = new ListUsersResponse($users);

    expect($response)->toBeInstanceOf(ListUsersResponseInterface::class);
    expect($response->getUsers())->toBeInstanceOf(Users::class);
    expect($response->getUsers())->toHaveCount(0);
});

test('ListUsersResponse handles single user data', function (): void {
    $object = (object) ['id' => 'user:alice'];
    $user = new User(object: $object);
    $users = new Users([$user]);
    $response = new ListUsersResponse($users);

    expect($response)->toBeInstanceOf(ListUsersResponseInterface::class);
    expect($response->getUsers())->toHaveCount(1);
});

// Removed fromResponse error handling test - handled in integration tests

// Removed fromResponse validation error test - handled in integration tests

// Removed fromResponse missing field test - handled in integration tests

test('ListUsersResponse handles large user collections', function (): void {
    $users = [];
    for ($i = 0; $i < 100; ++$i) {
        $object = (object) ['id' => "user:user{$i}"];
        $users[] = new User(object: $object);
    }
    $usersCollection = new Users($users);
    $response = new ListUsersResponse($usersCollection);

    expect($response->getUsers())->toHaveCount(100);
    expect($response->getUsers()->first()->getObject()->id)->toBe('user:user0');
});

test('ListUsersResponse handles users with complex object identifiers', function (): void {
    $object1 = (object) ['id' => 'team:engineering#member'];
    $object2 = (object) ['id' => 'organization:acme#admin'];
    $object3 = (object) ['id' => 'document:doc1#viewer'];
    $user1 = new User(object: $object1);
    $user2 = new User(object: $object2);
    $user3 = new User(object: $object3);
    $users = new Users([$user1, $user2, $user3]);

    $response = new ListUsersResponse($users);

    expect($response->getUsers())->toHaveCount(3);
    expect($response->getUsers()->toArray())->toBe([$user1, $user2, $user3]);
});
