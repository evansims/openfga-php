<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models\Collections;

use OpenFGA\Models\Collections\{UsersList, UsersListInterface};
use OpenFGA\Models\UsersListUser;
use OpenFGA\Schemas\{CollectionSchemaInterface, SchemaInterface};

describe('UsersList Collection', function (): void {
    test('implements interface', function (): void {
        $collection = new UsersList([]);

        expect($collection)->toBeInstanceOf(UsersListInterface::class);
    });

    test('creates empty', function (): void {
        $collection = new UsersList([]);

        expect($collection->count())->toBe(0);
        expect($collection->isEmpty())->toBeTrue();
    });

    test('creates with array of users', function (): void {
        $user1 = new UsersListUser(user: 'user:alice');
        $user2 = new UsersListUser(user: 'user:bob');
        $user3 = new UsersListUser(user: 'group:engineering#member');

        $collection = new UsersList([$user1, $user2, $user3]);

        expect($collection->count())->toBe(3);
        expect($collection->isEmpty())->toBeFalse();
    });

    test('adds users', function (): void {
        $collection = new UsersList([]);

        $user = new UsersListUser(user: 'user:charlie');

        $collection->add($user);

        expect($collection->count())->toBe(1);
        expect($collection->get(0))->toBe($user);
    });

    test('gets users by index', function (): void {
        $user1 = new UsersListUser(user: 'user:alice');
        $user2 = new UsersListUser(user: 'user:bob');

        $collection = new UsersList([$user1, $user2]);

        expect($collection->get(0))->toBe($user1);
        expect($collection->get(1))->toBe($user2);
        expect($collection->get(2))->toBeNull();
    });

    test('checks if user exists', function (): void {
        $user = new UsersListUser(user: 'user:test');

        $collection = new UsersList([$user]);

        expect(isset($collection[0]))->toBeTrue();
        expect(isset($collection[1]))->toBeFalse();
    });

    test('iterates over users', function (): void {
        $user1 = new UsersListUser(user: 'user:alice');
        $user2 = new UsersListUser(user: 'user:bob');
        $user3 = new UsersListUser(user: 'user:charlie');

        $collection = new UsersList([$user1, $user2, $user3]);

        $userIds = [];

        foreach ($collection as $user) {
            $userIds[] = $user->getUser();
        }

        expect($userIds)->toBe(['user:alice', 'user:bob', 'user:charlie']);
    });

    test('toArray', function (): void {
        $user1 = new UsersListUser(user: 'user:alice');
        $user2 = new UsersListUser(user: 'user:bob');

        $collection = new UsersList([$user1, $user2]);
        $array = $collection->toArray();

        expect($array)->toBeArray();
        expect($array)->toHaveCount(2);
        expect($array[0])->toBe($user1);
        expect($array[1])->toBe($user2);
    });

    test('jsonSerialize', function (): void {
        $collection = new UsersList([
            new UsersListUser(user: 'user:alice'),
            new UsersListUser(user: 'user:bob'),
            new UsersListUser(user: 'group:engineering#member'),
            new UsersListUser(user: 'user:*'),
        ]);

        $json = $collection->jsonSerialize();

        expect($json)->toBeArray();
        expect($json)->toHaveCount(4);

        expect($json)->toBe([
            'user:alice',
            'user:bob',
            'group:engineering#member',
            'user:*',
        ]);
    });

    test('handles different user identifier formats', function (): void {
        $collection = new UsersList([
            new UsersListUser(user: 'user:alice'),
            new UsersListUser(user: 'user:*'),
            new UsersListUser(user: 'group:engineering#member'),
            new UsersListUser(user: 'team:platform#owner'),
            new UsersListUser(user: 'service:api-server'),
            new UsersListUser(user: 'organization:acme#admin'),
        ]);

        expect($collection->count())->toBe(6);

        $types = [];

        foreach ($collection as $user) {
            $identifier = $user->getUser();
            $type = explode(':', $identifier)[0];
            $types[] = $type;
        }

        expect($types)->toBe(['user', 'user', 'group', 'team', 'service', 'organization']);
    });

    test('filters users by type prefix', function (): void {
        $collection = new UsersList([
            new UsersListUser(user: 'user:alice'),
            new UsersListUser(user: 'group:engineering#member'),
            new UsersListUser(user: 'user:bob'),
            new UsersListUser(user: 'group:sales#member'),
            new UsersListUser(user: 'user:charlie'),
        ]);

        $directUsers = [];

        foreach ($collection as $user) {
            if (str_starts_with($user->getUser(), 'user:')) {
                $directUsers[] = $user->getUser();
            }
        }

        expect($directUsers)->toBe(['user:alice', 'user:bob', 'user:charlie']);
    });

    test('handles wildcard users', function (): void {
        $collection = new UsersList([
            new UsersListUser(user: 'user:alice'),
            new UsersListUser(user: 'user:*'),
            new UsersListUser(user: 'group:*#member'),
            new UsersListUser(user: 'user:bob'),
        ]);

        $wildcardCount = 0;

        foreach ($collection as $user) {
            if (str_contains($user->getUser(), '*')) {
                ++$wildcardCount;
            }
        }

        expect($wildcardCount)->toBe(2);
    });

    test('string representation', function (): void {
        $user = new UsersListUser(user: 'user:alice');

        expect((string) $user)->toBe('user:alice');
    });

    test('schema', function (): void {
        $schema = UsersList::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema)->toBeInstanceOf(CollectionSchemaInterface::class);
        expect($schema->getClassName())->toBe(UsersList::class);
    });

    test('schema is cached', function (): void {
        $schema1 = UsersList::schema();
        $schema2 = UsersList::schema();

        expect($schema1)
            ->toBeInstanceOf(CollectionSchemaInterface::class)
            ->toBe($schema2, 'Expected the same schema instance to be returned on subsequent calls');
    });

    test('handles empty collection edge cases', function (): void {
        $collection = new UsersList([]);

        expect($collection->isEmpty())->toBeTrue();
        expect($collection->toArray())->toBe([]);
        expect($collection->jsonSerialize())->toBe([]);

        $count = 0;

        foreach ($collection as $_) {
            ++$count;
        }
        expect($count)->toBe(0);

        expect($collection->get(0))->toBeNull();
    });

    test('represents access control scenarios', function (): void {
        $collection = new UsersList([
            new UsersListUser(user: 'user:alice'),
            new UsersListUser(user: 'user:bob'),
            new UsersListUser(user: 'group:engineering#member'),
            new UsersListUser(user: 'group:product#member'),
            new UsersListUser(user: 'user:*'),
            new UsersListUser(user: 'service:backup-agent'),
        ]);

        expect($collection->count())->toBe(6);

        $hasPublicAccess = false;

        foreach ($collection as $user) {
            if ('user:*' === $user->getUser()) {
                $hasPublicAccess = true;

                break;
            }
        }

        expect($hasPublicAccess)->toBeTrue();
    });
});
