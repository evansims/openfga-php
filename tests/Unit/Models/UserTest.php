<?php

declare(strict_types=1);

use OpenFGA\Models\{TypedWildcard, User, UserInterface, UsersetUser};
use OpenFGA\Schema\SchemaInterface;

describe('User Model', function (): void {
    test('implements UserInterface', function (): void {
        $user = new User();

        expect($user)->toBeInstanceOf(UserInterface::class);
    });

    test('constructs with all null parameters', function (): void {
        $user = new User();

        expect($user->getObject())->toBeNull();
        expect($user->getUserset())->toBeNull();
        expect($user->getWildcard())->toBeNull();
    });

    test('constructs with object', function (): void {
        $object = new stdClass();
        $object->id = 'user:anne';
        $user = new User(object: $object);

        expect($user->getObject())->toBe($object);
        expect($user->getUserset())->toBeNull();
        expect($user->getWildcard())->toBeNull();
    });

    test('constructs with userset', function (): void {
        $userset = new UsersetUser(type: 'group', id: 'admins', relation: 'member');
        $user = new User(userset: $userset);

        expect($user->getObject())->toBeNull();
        expect($user->getUserset())->toBe($userset);
        expect($user->getWildcard())->toBeNull();
    });

    test('constructs with wildcard', function (): void {
        $wildcard = new TypedWildcard(type: 'user');
        $user = new User(wildcard: $wildcard);

        expect($user->getObject())->toBeNull();
        expect($user->getUserset())->toBeNull();
        expect($user->getWildcard())->toBe($wildcard);
    });

    test('serializes to JSON with null fields', function (): void {
        $user = new User();
        expect($user->jsonSerialize())->toBe([]);
    });

    test('serializes to JSON with object implementing JsonSerializable', function (): void {
        $object = new class() implements JsonSerializable {
            public function jsonSerialize(): array
            {
                return ['id' => 'user:anne'];
            }
        };

        $user = new User(object: $object);
        expect($user->jsonSerialize())->toBe(['object' => ['id' => 'user:anne']]);
    });

    test('serializes to JSON with object implementing __toString', function (): void {
        $object = new class() {
            public function __toString(): string
            {
                return 'user:anne';
            }
        };

        $user = new User(object: $object);
        expect($user->jsonSerialize())->toBe(['object' => 'user:anne']);
    });

    test('serializes to JSON with plain object', function (): void {
        $object = new stdClass();
        $object->type = 'user';
        $object->id = 'anne';

        $user = new User(object: $object);
        expect($user->jsonSerialize())->toBe(['object' => ['type' => 'user', 'id' => 'anne']]);
    });

    test('serializes to JSON with userset', function (): void {
        $userset = new UsersetUser(type: 'group', id: 'admins', relation: 'member');
        $user = new User(userset: $userset);

        $json = $user->jsonSerialize();
        expect($json)->toHaveKey('userset');
        expect($json['userset'])->toBe(['type' => 'group', 'id' => 'admins', 'relation' => 'member']);
    });

    test('serializes to JSON with wildcard', function (): void {
        $wildcard = new TypedWildcard(type: 'user');
        $user = new User(wildcard: $wildcard);

        $json = $user->jsonSerialize();
        expect($json)->toHaveKey('wildcard');
        expect($json['wildcard'])->toBe(['type' => 'user']);
    });

    test('returns schema instance', function (): void {
        $schema = User::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema->getClassName())->toBe(User::class);

        $properties = $schema->getProperties();
        expect($properties)->toHaveCount(4); // object, userset, wildcard, difference
    });

    test('schema properties have correct configuration', function (): void {
        $schema = User::schema();
        $properties = $schema->getProperties();

        $propertyNames = array_keys($properties);
        expect($propertyNames)->toBe(['object', 'userset', 'wildcard', 'difference']);

        // Object property
        $objectProp = $properties['object'];
        expect($objectProp->name)->toBe('object');
        expect($objectProp->type)->toBe('object');
        expect($objectProp->required)->toBe(false);

        // Userset property
        $usersetProp = $properties['userset'];
        expect($usersetProp->name)->toBe('userset');
        expect($usersetProp->type)->toBe('object');
        expect($usersetProp->required)->toBe(false);

        // Wildcard property
        $wildcardProp = $properties['wildcard'];
        expect($wildcardProp->name)->toBe('wildcard');
        expect($wildcardProp->type)->toBe('object');
        expect($wildcardProp->required)->toBe(false);

        // Difference property
        $differenceProp = $properties['difference'];
        expect($differenceProp->name)->toBe('difference');
        expect($differenceProp->type)->toBe('object');
        expect($differenceProp->required)->toBe(false);
    });

    test('schema is cached', function (): void {
        $schema1 = User::schema();
        $schema2 = User::schema();

        expect($schema1)->toBe($schema2);
    });

    test('only one field should be set at a time', function (): void {
        $user1 = new User(object: new stdClass());
        $user2 = new User(userset: new UsersetUser(type: 'group', id: 'admins', relation: 'member'));
        $user3 = new User(wildcard: new TypedWildcard(type: 'user'));

        expect($user1->jsonSerialize())->toHaveCount(1);
        expect($user2->jsonSerialize())->toHaveCount(1);
        expect($user3->jsonSerialize())->toHaveCount(1);
    });

    test('handles object with no serialization methods', function (): void {
        $object = new class() {
            private string $secret = 'hidden';

            public string $name = 'anne';
        };

        $user = new User(object: $object);
        $json = $user->jsonSerialize();

        expect($json['object'])->toBe(['name' => 'anne']);
        expect($json['object'])->not->toHaveKey('secret');
    });
});
