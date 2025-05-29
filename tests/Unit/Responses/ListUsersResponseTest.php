<?php

declare(strict_types=1);

use OpenFGA\Models\Collections\Users;
use OpenFGA\Models\User;
use OpenFGA\Responses\{ListUsersResponse, ListUsersResponseInterface};
use OpenFGA\Schema\{SchemaInterface, SchemaValidator};
use OpenFGA\Tests\Support\Responses\SimpleResponse;
use Psr\Http\Message\RequestInterface;

describe('ListUsersResponse', function (): void {
    test('implements ListUsersResponseInterface', function (): void {
        $users = new Users([]);
        $response = new ListUsersResponse($users);

        expect($response)->toBeInstanceOf(ListUsersResponseInterface::class);
    });

    test('constructs with users collection', function (): void {
        $object1 = (object) ['id' => 'user:alice'];
        $object2 = (object) ['id' => 'user:bob'];
        $user1 = new User(object: $object1);
        $user2 = new User(object: $object2);
        $users = new Users([$user1, $user2]);

        $response = new ListUsersResponse($users);

        expect($response->getUsers())->toBe($users);
        expect($response->getUsers())->toHaveCount(2);
    });

    test('constructs with empty users collection', function (): void {
        $users = new Users([]);
        $response = new ListUsersResponse($users);

        expect($response->getUsers())->toBe($users);
        expect($response->getUsers())->toHaveCount(0);
    });

    test('handles single user', function (): void {
        $object = (object) ['id' => 'user:alice'];
        $user = new User(object: $object);
        $users = new Users([$user]);
        $response = new ListUsersResponse($users);

        expect($response->getUsers())->toHaveCount(1);
        expect($response->getUsers()->first())->toBe($user);
    });

    test('handles multiple users', function (): void {
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

    test('schema returns correct structure', function (): void {
        $schema = ListUsersResponse::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema->getClassName())->toBe(ListUsersResponse::class);

        $properties = $schema->getProperties();
        expect($properties)->toHaveCount(1);
        expect($properties)->toHaveKey('users');

        expect($properties['users']->name)->toBe('users');
        expect($properties['users']->type)->toBe('object');
        expect($properties['users']->required)->toBeTrue();
    });

    test('schema is cached', function (): void {
        $schema1 = ListUsersResponse::schema();
        $schema2 = ListUsersResponse::schema();

        expect($schema1)->toBe($schema2);
    });

    // Note: fromResponse method testing would require integration tests due to SchemaValidator complexity
    // These tests focus on the model's direct functionality

    test('handles empty users array data', function (): void {
        $users = new Users([]);
        $response = new ListUsersResponse($users);

        expect($response)->toBeInstanceOf(ListUsersResponseInterface::class);
        expect($response->getUsers())->toHaveCount(0);
    });

    test('handles single user data', function (): void {
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

    test('handles large user collections', function (): void {
        $users = [];
        for ($i = 0; $i < 100; ++$i) {
            $object = (object) ['id' => "user:user{$i}"];
            $users[] = new User(object: $object);
        }
        $usersCollection = new Users($users);
        $response = new ListUsersResponse($usersCollection);

        expect($response->getUsers())->toHaveCount(100);
        expect($response->getUsers()->first()->getObject())->toBe('user:user0');
    });

    test('handles users with complex object identifiers', function (): void {
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

    test('fromResponse handles error responses with non-200 status', function (): void {
        $httpResponse = new SimpleResponse(400, json_encode(['code' => 'invalid_request', 'message' => 'Bad request']));
        $request = Mockery::mock(RequestInterface::class);
        $validator = new SchemaValidator();

        $this->expectException(OpenFGA\Exceptions\NetworkException::class);
        ListUsersResponse::fromResponse($httpResponse, $request, $validator);
    });

    test('fromResponse handles 401 unauthorized', function (): void {
        $httpResponse = new SimpleResponse(401, json_encode(['code' => 'unauthenticated', 'message' => 'Invalid credentials']));
        $request = Mockery::mock(RequestInterface::class);
        $validator = new SchemaValidator();

        $this->expectException(OpenFGA\Exceptions\NetworkException::class);
        ListUsersResponse::fromResponse($httpResponse, $request, $validator);
    });

    test('fromResponse handles 500 internal server error', function (): void {
        $httpResponse = new SimpleResponse(500, json_encode(['code' => 'internal_error', 'message' => 'Server error']));
        $request = Mockery::mock(RequestInterface::class);
        $validator = new SchemaValidator();

        $this->expectException(OpenFGA\Exceptions\NetworkException::class);
        ListUsersResponse::fromResponse($httpResponse, $request, $validator);
    });
});
