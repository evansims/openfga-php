<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models;

use OpenFGA\Models\{UserObject, UserObjectInterface};
use OpenFGA\Schemas\SchemaInterface;

use function strlen;

describe('UserObject Model', function (): void {
    test('implements UserObjectInterface', function (): void {
        $userObject = new UserObject(type: 'user', id: '123');

        expect($userObject)->toBeInstanceOf(UserObjectInterface::class);
    });

    test('constructs with type and id', function (): void {
        $userObject = new UserObject(type: 'user', id: '456');

        expect($userObject->getType())->toBe('user');
        expect($userObject->getId())->toBe('456');
    });

    test('constructs with different types', function (): void {
        $types = ['user', 'group', 'service-account', 'api-key', 'token'];

        foreach ($types as $type) {
            $userObject = new UserObject(type: $type, id: 'test-id');
            expect($userObject->getType())->toBe($type);
        }
    });

    test('constructs with various id formats', function (): void {
        $ids = [
            '123',
            'uuid-550e8400-e29b-41d4-a716-446655440000',
            'email@example.com',
            'user_with_underscore',
            'user-with-dash',
            'user.with.dot',
            'user:with:colon',
            'user/with/slash',
        ];

        foreach ($ids as $id) {
            $userObject = new UserObject(type: 'user', id: $id);
            expect($userObject->getId())->toBe($id);
        }
    });

    test('converts to string with colon separator', function (): void {
        $userObject = new UserObject(type: 'user', id: '123');

        expect((string) $userObject)->toBe('user:123');
        expect($userObject->__toString())->toBe('user:123');
    });

    test('handles empty strings', function (): void {
        $userObject1 = new UserObject(type: '', id: '123');
        expect((string) $userObject1)->toBe(':123');

        $userObject2 = new UserObject(type: 'user', id: '');
        expect((string) $userObject2)->toBe('user:');

        $userObject3 = new UserObject(type: '', id: '');
        expect((string) $userObject3)->toBe(':');
    });

    test('handles special characters in string representation', function (): void {
        $userObject = new UserObject(type: 'user:type', id: 'id:with:colons');
        expect((string) $userObject)->toBe('user:type:id:with:colons');
    });

    test('serializes to JSON', function (): void {
        $userObject = new UserObject(type: 'user', id: '789');
        $json = $userObject->jsonSerialize();

        expect($json)->toBe([
            'type' => 'user',
            'id' => '789',
        ]);
    });

    test('returns schema instance', function (): void {
        $schema = UserObject::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema->getClassName())->toBe(UserObject::class);

        $properties = $schema->getProperties();
        expect($properties)->toHaveCount(2);

        // Check type property
        $typeProperty = $properties['type'] ?? null;
        expect($typeProperty)->not->toBeNull();
        expect($typeProperty->name)->toBe('type');
        expect($typeProperty->type)->toBe('string');
        expect($typeProperty->required)->toBe(true);

        // Check id property
        $idProperty = $properties['id'] ?? null;
        expect($idProperty)->not->toBeNull();
        expect($idProperty->name)->toBe('id');
        expect($idProperty->type)->toBe('string');
        expect($idProperty->required)->toBe(true);
    });

    test('schema is cached', function (): void {
        $schema1 = UserObject::schema();
        $schema2 = UserObject::schema();

        expect($schema1)->toBe($schema2);
    });

    test('maintains immutability', function (): void {
        $userObject = new UserObject(type: 'user', id: '123');

        // Get values multiple times to ensure they don't change
        $type1 = $userObject->getType();
        $id1 = $userObject->getId();
        $string1 = (string) $userObject;
        $json1 = $userObject->jsonSerialize();

        $type2 = $userObject->getType();
        $id2 = $userObject->getId();
        $string2 = (string) $userObject;
        $json2 = $userObject->jsonSerialize();

        expect($type1)->toBe($type2);
        expect($id1)->toBe($id2);
        expect($string1)->toBe($string2);
        expect($json1)->toBe($json2);
    });

    test('handles unicode characters', function (): void {
        $userObject = new UserObject(type: 'user', id: '用户123');

        expect($userObject->getType())->toBe('user');
        expect($userObject->getId())->toBe('用户123');
        expect((string) $userObject)->toBe('user:用户123');
    });

    test('handles long strings', function (): void {
        $longId = str_repeat('a', 1000);
        $userObject = new UserObject(type: 'user', id: $longId);

        expect($userObject->getId())->toBe($longId);
        expect(strlen((string) $userObject))->toBe(1005); // 'user:' + 1000 chars
    });

    test('properly represents different object types', function (): void {
        $examples = [
            ['type' => 'user', 'id' => 'anne', 'expected' => 'user:anne'],
            ['type' => 'group', 'id' => 'admins', 'expected' => 'group:admins'],
            ['type' => 'folder', 'id' => 'root', 'expected' => 'folder:root'],
            ['type' => 'document', 'id' => 'roadmap.pdf', 'expected' => 'document:roadmap.pdf'],
        ];

        foreach ($examples as $example) {
            $userObject = new UserObject(type: $example['type'], id: $example['id']);
            expect((string) $userObject)->toBe($example['expected']);
        }
    });
});
