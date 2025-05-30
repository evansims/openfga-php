<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models;

use JsonSerializable;
use OpenFGA\Models\{DifferenceV1, ObjectRelation, TypedWildcard, User, UserInterface, UserObject, Userset, UsersetUser};
use OpenFGA\Schema\SchemaInterface;
use stdClass;

describe('User Model', function (): void {
    test('implements UserInterface', function (): void {
        $user = new User;

        expect($user)->toBeInstanceOf(UserInterface::class);
    });

    test('constructs with all null parameters', function (): void {
        $user = new User;

        expect($user->getObject())->toBeNull();
        expect($user->getUserset())->toBeNull();
        expect($user->getWildcard())->toBeNull();
        expect($user->getDifference())->toBeNull();
    });

    test('constructs with object', function (): void {
        $object = new stdClass;
        $object->id = 'user:anne';
        $user = new User(object: $object);

        expect($user->getObject())->toBe('user:anne');
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
        expect($user->getDifference())->toBeNull();
    });

    test('constructs with difference', function (): void {
        $base = new Userset(computedUserset: new ObjectRelation(relation: 'editor'));
        $subtract = new Userset(computedUserset: new ObjectRelation(relation: 'blocked'));
        $difference = new DifferenceV1(base: $base, subtract: $subtract);
        $user = new User(difference: $difference);

        expect($user->getObject())->toBeNull();
        expect($user->getUserset())->toBeNull();
        expect($user->getWildcard())->toBeNull();
        expect($user->getDifference())->toBe($difference);
    });

    test('serializes to JSON with null fields', function (): void {
        $user = new User;
        expect($user->jsonSerialize())->toBe([]);
    });

    test('serializes to JSON with object implementing JsonSerializable', function (): void {
        $object = new class implements JsonSerializable {
            public function jsonSerialize(): array
            {
                return ['id' => 'user:anne'];
            }
        };

        $user = new User(object: $object);
        expect($user->jsonSerialize())->toBe(['object' => ['id' => 'user:anne']]);
    });

    test('serializes to JSON with object implementing __toString', function (): void {
        $object = new class {
            public function __toString(): string
            {
                return 'user:anne';
            }
        };

        $user = new User(object: $object);
        expect($user->jsonSerialize())->toBe(['object' => 'user:anne']);
    });

    test('serializes to JSON with plain object', function (): void {
        $object = new stdClass;
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

    test('serializes to JSON with difference', function (): void {
        $base = new Userset(computedUserset: new ObjectRelation(relation: 'editor'));
        $subtract = new Userset(computedUserset: new ObjectRelation(relation: 'blocked'));
        $difference = new DifferenceV1(base: $base, subtract: $subtract);
        $user = new User(difference: $difference);

        $json = $user->jsonSerialize();
        expect($json)->toHaveKey('difference');
        expect($json['difference'])->toBe([
            'base' => ['computedUserset' => ['relation' => 'editor']],
            'subtract' => ['computedUserset' => ['relation' => 'blocked']],
        ]);
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
        $user1 = new User(object: new stdClass);
        $user2 = new User(userset: new UsersetUser(type: 'group', id: 'admins', relation: 'member'));
        $user3 = new User(wildcard: new TypedWildcard(type: 'user'));

        $base = new Userset(computedUserset: new ObjectRelation(relation: 'editor'));
        $subtract = new Userset(computedUserset: new ObjectRelation(relation: 'blocked'));
        $difference = new DifferenceV1(base: $base, subtract: $subtract);
        $user4 = new User(difference: $difference);

        expect($user1->jsonSerialize())->toHaveCount(1);
        expect($user2->jsonSerialize())->toHaveCount(1);
        expect($user3->jsonSerialize())->toHaveCount(1);
        expect($user4->jsonSerialize())->toHaveCount(1);
    });

    test('handles object with no serialization methods', function (): void {
        $object = new class {
            public string $name = 'anne';

            private string $secret = 'hidden';
        };

        $user = new User(object: $object);
        $json = $user->jsonSerialize();

        expect($json['object'])->toBe(['name' => 'anne']);
        expect($json['object'])->not->toHaveKey('secret');
    });

    test('getObject returns UserObject when object has type and id properties', function (): void {
        $object = new class {
            public string $id = '123';

            public string $type = 'user';
        };

        $user = new User(object: $object);
        $result = $user->getObject();

        expect($result)->toBeInstanceOf(UserObject::class);
        expect($result->getType())->toBe('user');
        expect($result->getId())->toBe('123');
    });

    test('getObject returns UserObject when object is array with type and id', function (): void {
        $object = [
            'type' => 'group',
            'id' => 'admins',
        ];

        $user = new User(object: $object);
        $result = $user->getObject();

        expect($result)->toBeInstanceOf(UserObject::class);
        expect($result->getType())->toBe('group');
        expect($result->getId())->toBe('admins');
    });

    test('getObject returns string when object has toString method', function (): void {
        $object = new class {
            public function __toString(): string
            {
                return 'user:anne';
            }
        };

        $user = new User(object: $object);
        $result = $user->getObject();

        expect($result)->toBe('user:anne');
    });

    test('getObject returns null when object cannot be converted', function (): void {
        // Object without type/id, toString, or valid structure
        $object = new class {
            public string $name = 'invalid';

            public int $value = 42;
        };

        $user = new User(object: $object);
        $result = $user->getObject();

        expect($result)->toBeNull();
    });

    test('getObject returns string directly when object is string', function (): void {
        $user = new User(object: 'user:anne');
        $result = $user->getObject();

        expect($result)->toBe('user:anne');
    });

    test('getObject returns UserObjectInterface directly', function (): void {
        $userObject = new UserObject(type: 'user', id: '456');
        $user = new User(object: $userObject);
        $result = $user->getObject();

        expect($result)->toBe($userObject);
    });

    test('getObject handles array with missing id', function (): void {
        $object = [
            'type' => 'user',
            // missing 'id'
        ];

        $user = new User(object: $object);
        $result = $user->getObject();

        expect($result)->toBeNull();
    });

    test('getObject handles array with missing type', function (): void {
        $object = [
            'id' => '123',
            // missing 'type'
        ];

        $user = new User(object: $object);
        $result = $user->getObject();

        expect($result)->toBeNull();
    });

    test('getObject handles object with non-string type or id', function (): void {
        $object = new class {
            public string $id = 'test';

            public int $type = 123;
        };

        $user = new User(object: $object);
        $result = $user->getObject();

        // Since it has an id property that is string, it returns that
        expect($result)->toBe('test');
    });
});
