<?php

declare(strict_types=1);

use OpenFGA\Models\Collections\{Users, UsersInterface, UsersList};
use OpenFGA\Models\{TypedWildcard, User, UsersetUser};
use OpenFGA\Schema\{CollectionSchemaInterface, SchemaInterface};

describe('Users Collection', function (): void {
    test('implements UsersInterface', function (): void {
        $collection = new Users([]);

        expect($collection)->toBeInstanceOf(UsersInterface::class);
    });

    test('constructs with empty array', function (): void {
        $collection = new Users([]);

        expect($collection->count())->toBe(0);
        expect($collection->isEmpty())->toBeTrue();
    });

    test('constructs with array of users', function (): void {
        $user1 = new User(object: new stdClass());
        $user2 = new User(wildcard: new TypedWildcard(type: 'user'));
        $user3 = new User(userset: new UsersetUser(
            type: 'group',
            id: 'engineering',
            relation: 'member',
        ));

        $collection = new Users([$user1, $user2, $user3]);

        expect($collection->count())->toBe(3);
        expect($collection->isEmpty())->toBeFalse();
    });

    test('adds users', function (): void {
        $collection = new Users([]);

        $user = new User(object: new stdClass());

        $collection->add($user);

        expect($collection->count())->toBe(1);
        expect($collection->get(0))->toBe($user);
    });

    test('gets users by index', function (): void {
        $user1 = new User(object: new stdClass());
        $user2 = new User(wildcard: new TypedWildcard(type: 'user'));

        $collection = new Users([$user1, $user2]);

        expect($collection->get(0))->toBe($user1);
        expect($collection->get(1))->toBe($user2);
        expect($collection->get(2))->toBeNull();
    });

    test('checks if user exists', function (): void {
        $user = new User(object: new stdClass());

        $collection = new Users([$user]);

        expect(isset($collection[0]))->toBeTrue();
        expect(isset($collection[1]))->toBeFalse();
    });

    test('iterates over users', function (): void {
        $user1 = new User(object: new stdClass());
        $user2 = new User(wildcard: new TypedWildcard(type: 'user'));
        $user3 = new User(userset: new UsersetUser(
            type: 'team',
            id: 'platform',
            relation: 'owner',
        ));

        $collection = new Users([$user1, $user2, $user3]);

        $count = 0;
        foreach ($collection as $user) {
            expect($user)->toBeInstanceOf(User::class);
            ++$count;
        }

        expect($count)->toBe(3);
    });

    test('converts to array', function (): void {
        $user1 = new User(object: new stdClass());
        $user2 = new User(wildcard: new TypedWildcard(type: 'group'));

        $collection = new Users([$user1, $user2]);
        $array = $collection->toArray();

        expect($array)->toBeArray();
        expect($array)->toHaveCount(2);
        expect($array[0])->toBe($user1);
        expect($array[1])->toBe($user2);
    });

    test('serializes to JSON', function (): void {
        $collection = new Users([
            new User(object: new stdClass()),
            new User(wildcard: new TypedWildcard(type: 'user')),
            new User(userset: new UsersetUser(
                type: 'group',
                id: 'engineering',
                relation: 'member',
            )),
        ]);

        $json = $collection->jsonSerialize();

        expect($json)->toBeArray();
        expect($json)->toHaveCount(3);

        // Check first user (object)
        expect($json[0])->toHaveKey('object');
        expect($json[0]['object'])->toBe([]);

        // Check second user (wildcard)
        expect($json[1])->toHaveKey('wildcard');
        expect($json[1]['wildcard'])->toBe(['type' => 'user']);

        // Check third user (userset)
        expect($json[2])->toHaveKey('userset');
        expect($json[2]['userset'])->toBe([
            'type' => 'group',
            'id' => 'engineering',
            'relation' => 'member',
        ]);
    });

    test('handles different user types', function (): void {
        $collection = new Users([
            // Object user
            new User(object: new stdClass()),

            // Wildcard users
            new User(wildcard: new TypedWildcard(type: 'user')),
            new User(wildcard: new TypedWildcard(type: 'group')),

            // Userset users
            new User(userset: new UsersetUser(
                type: 'group',
                id: 'engineering',
                relation: 'member',
            )),
            new User(userset: new UsersetUser(
                type: 'team',
                id: 'platform',
                relation: 'owner',
            )),
        ]);

        expect($collection->count())->toBe(5);

        // Count each type
        $objectCount = 0;
        $wildcardCount = 0;
        $usersetCount = 0;

        foreach ($collection as $user) {
            if (null !== $user->getObject()) {
                ++$objectCount;
            } elseif (null !== $user->getWildcard()) {
                ++$wildcardCount;
            } elseif (null !== $user->getUserset()) {
                ++$usersetCount;
            }
        }

        expect($objectCount)->toBe(1);
        expect($wildcardCount)->toBe(2);
        expect($usersetCount)->toBe(2);
    });

    test('returns schema instance', function (): void {
        $schema = Users::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema)->toBeInstanceOf(CollectionSchemaInterface::class);
        expect($schema->getClassName())->toBe(Users::class);
    });

    test('schema is cached', function (): void {
        $schema1 = Users::schema();
        $schema2 = Users::schema();

        expect($schema1)
            ->toBeInstanceOf(CollectionSchemaInterface::class)
            ->toBe($schema2, 'Expected the same schema instance to be returned on subsequent calls');
    });

    test('schemas from different collection classes are different instances', function (): void {
        $usersSchema = Users::schema();
        $usersListSchema = UsersList::schema();

        expect($usersSchema)
            ->not->toBe($usersListSchema, 'Expected different collection classes to have different schema instances');
    });

    test('handles empty collection edge cases', function (): void {
        $collection = new Users([]);

        expect($collection->isEmpty())->toBeTrue();
        expect($collection->toArray())->toBe([]);
        expect($collection->jsonSerialize())->toBe([]);

        // Test iteration on empty collection
        $count = 0;
        foreach ($collection as $_) {
            ++$count;
        }
        expect($count)->toBe(0);

        // Test get on empty collection
        expect($collection->get(0))->toBeNull();
    });
});
