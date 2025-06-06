<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models;

use OpenFGA\Models\{UsersListUser, UsersListUserInterface};
use OpenFGA\Schemas\SchemaInterface;

describe('UsersListUser Model', function (): void {
    test('implements UsersListUserInterface', function (): void {
        $user = new UsersListUser(user: 'user:alice');

        expect($user)->toBeInstanceOf(UsersListUserInterface::class);
    });

    test('constructs with user string', function (): void {
        $user = new UsersListUser(user: 'user:alice');

        expect($user->getUser())->toBe('user:alice');
    });

    test('serializes to string', function (): void {
        $user = new UsersListUser(user: 'user:alice');

        expect($user->jsonSerialize())->toBe('user:alice');
    });

    test('returns schema instance', function (): void {
        $schema = UsersListUser::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema->getClassName())->toBe(UsersListUser::class);

        $properties = $schema->getProperties();
        expect($properties)->toHaveCount(1);

        $propertyNames = array_keys($properties);
        expect($propertyNames)->toBe(['user']);
    });

    test('schema properties have correct configuration', function (): void {
        $schema = UsersListUser::schema();
        $properties = $schema->getProperties();

        // User property
        $userProp = $properties['user'];
        expect($userProp->name)->toBe('user');
        expect($userProp->type)->toBe('string');
        expect($userProp->required)->toBe(true);
    });

    test('schema is cached', function (): void {
        $schema1 = UsersListUser::schema();
        $schema2 = UsersListUser::schema();

        expect($schema1)->toBe($schema2);
    });

    test('handles various user formats', function (): void {
        // Simple user ID
        $simpleUser = new UsersListUser(user: 'user:alice');
        expect($simpleUser->getUser())->toBe('user:alice');
        expect($simpleUser->jsonSerialize())->toBe('user:alice');

        // User with wildcard
        $wildcardUser = new UsersListUser(user: 'user:*');
        expect($wildcardUser->getUser())->toBe('user:*');
        expect($wildcardUser->jsonSerialize())->toBe('user:*');

        // Group member
        $groupMember = new UsersListUser(user: 'group:engineering#member');
        expect($groupMember->getUser())->toBe('group:engineering#member');
        expect($groupMember->jsonSerialize())->toBe('group:engineering#member');

        // Userset reference
        $usersetRef = new UsersListUser(user: 'document:budget#viewer');
        expect($usersetRef->getUser())->toBe('document:budget#viewer');
        expect($usersetRef->jsonSerialize())->toBe('document:budget#viewer');
    });

    test('handles complex user identifiers', function (): void {
        // User with email-like ID
        $emailUser = new UsersListUser(user: 'user:alice@example.com');
        expect($emailUser->getUser())->toBe('user:alice@example.com');

        // User with UUID
        $uuidUser = new UsersListUser(user: 'user:550e8400-e29b-41d4-a716-446655440000');
        expect($uuidUser->getUser())->toBe('user:550e8400-e29b-41d4-a716-446655440000');

        // Nested relation
        $nestedRelation = new UsersListUser(user: 'team:engineering#parent#member');
        expect($nestedRelation->getUser())->toBe('team:engineering#parent#member');

        // Special characters in type
        $specialType = new UsersListUser(user: 'user_group:admin-team#member');
        expect($specialType->getUser())->toBe('user_group:admin-team#member');
    });

    test('preserves exact user string format', function (): void {
        // Test that no normalization or transformation happens
        $testCases = [
            'user:123',
            'group:abc#member',
            'organization:xyz#admin',
            'user:*',
            'team:dev#lead',
            'document:report#viewer',
            'folder:shared#editor',
        ];

        foreach ($testCases as $testCase) {
            $user = new UsersListUser(user: $testCase);
            expect($user->getUser())->toBe($testCase);
            expect($user->jsonSerialize())->toBe($testCase);
        }
    });

    test('handles edge cases', function (): void {
        // Empty string (though this might be invalid in practice)
        $emptyUser = new UsersListUser(user: '');
        expect($emptyUser->getUser())->toBe('');
        expect($emptyUser->jsonSerialize())->toBe('');

        // Very long user string
        $longId = str_repeat('a', 1000);
        $longUser = new UsersListUser(user: "user:{$longId}");
        expect($longUser->getUser())->toBe("user:{$longId}");

        // User with spaces (though this might be invalid in practice)
        $spacedUser = new UsersListUser(user: 'user:alice smith');
        expect($spacedUser->getUser())->toBe('user:alice smith');
    });
});
