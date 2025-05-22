<?php

namespace OpenFGATests\Unit\Models;

use OpenFGA\Models\User;
use OpenFGA\Models\UsersetUser;
use OpenFGA\Models\UsersetUserInterface;
use OpenFGA\Models\TypedWildcard;
use OpenFGA\Models\TypedWildcardInterface;
use OpenFGA\Models\DifferenceV1;
use OpenFGA\Schema\SchemaInterface;
use JsonSerializable;
use stdClass;

// Dummy Interfaces for testing User model

if (!interface_exists(UsersetUserInterface::class)) {
    interface UsersetUserInterface extends JsonSerializable {
        public function getType(): string;
        public function getId(): string;
        // Add other methods if User.php interacts with them beyond JsonSerializable
    }
}

if (!interface_exists(TypedWildcardInterface::class)) {
    interface TypedWildcardInterface extends JsonSerializable {
        public function getType(): string;
        // Add other methods if User.php interacts with them beyond JsonSerializable
    }
}


class DummyUsersetUser implements UsersetUserInterface {
    public function __construct(private string $type = 'userset_type', private string $id = 'dummy_id') {}

    public function getType(): string {
        return $this->type;
    }

    public function getId(): string {
        return $this->id;
    }

    public function jsonSerialize(): array {
        return ['type' => $this->type, 'id' => $this->id, 'relation' => null]; // Added relation as per UsersetUser
    }
}

class DummyTypedWildcard implements TypedWildcardInterface {
    public function __construct(private string $type = 'wildcard_type') {}

    public function getType(): string {
        return $this->type;
    }

    public function jsonSerialize(): array {
        return ['type' => $this->type];
    }
}

class DummyJsonSerializable implements JsonSerializable {
    public function jsonSerialize(): array {
        return ['data' => 'serializable'];
    }
}

class DummyToString {
    public function __toString(): string {
        return 'object_as_string';
    }
}

describe('User', function () {
    describe('constructor', function () {
        it('constructs with no parameters (all null)', function () {
            $user = new User();
            expect($user->getObject())->toBeNull()
                ->and($user->getUserset())->toBeNull()
                ->and($user->getWildcard())->toBeNull();
        });

        it('constructs with object parameter only (stdClass)', function () {
            $obj = new stdClass();
            $obj->property = 'value';
            $user = new User(object: $obj);
            expect($user->getObject())->toBe($obj)
                ->and($user->getUserset())->toBeNull()
                ->and($user->getWildcard())->toBeNull();
        });

        it('constructs with object parameter only (DummyJsonSerializable)', function () {
            $obj = new DummyJsonSerializable();
            $user = new User(object: $obj);
            expect($user->getObject())->toBe($obj);
        });

        it('constructs with object parameter only (DummyToString)', function () {
            $obj = new DummyToString();
            $user = new User(object: $obj);
            expect($user->getObject())->toBe($obj);
        });

        it('constructs with userset parameter only', function () {
            $userset = new DummyUsersetUser();
            $user = new User(userset: $userset);
            expect($user->getObject())->toBeNull()
                ->and($user->getUserset())->toBe($userset)
                ->and($user->getWildcard())->toBeNull();
        });

        it('constructs with wildcard parameter only', function () {
            $wildcard = new DummyTypedWildcard();
            $user = new User(wildcard: $wildcard);
            expect($user->getObject())->toBeNull()
                ->and($user->getUserset())->toBeNull()
                ->and($user->getWildcard())->toBe($wildcard);
        });

        it('constructs with all parameters set', function () {
            $obj = new stdClass();
            $userset = new DummyUsersetUser();
            $wildcard = new DummyTypedWildcard();
            $user = new User(object: $obj, userset: $userset, wildcard: $wildcard);
            expect($user->getObject())->toBe($obj)
                ->and($user->getUserset())->toBe($userset)
                ->and($user->getWildcard())->toBe($wildcard);
        });
    });

    describe('getters', function () {
        $obj = new stdClass();
        $userset = new DummyUsersetUser();
        $wildcard = new DummyTypedWildcard();
        $userWithAll = new User(object: $obj, userset: $userset, wildcard: $wildcard);
        $userEmpty = new User();

        it('getObject returns the correct value or null', function () use ($userWithAll, $obj, $userEmpty) {
            expect($userWithAll->getObject())->toBe($obj)
                ->and($userEmpty->getObject())->toBeNull();
        });

        it('getUserset returns the correct value or null', function () use ($userWithAll, $userset, $userEmpty) {
            expect($userWithAll->getUserset())->toBe($userset)
                ->and($userEmpty->getUserset())->toBeNull();
        });

        it('getWildcard returns the correct value or null', function () use ($userWithAll, $wildcard, $userEmpty) {
            expect($userWithAll->getWildcard())->toBe($wildcard)
                ->and($userEmpty->getWildcard())->toBeNull();
        });
    });

    describe('jsonSerialize', function () {
        it('serializes to an empty array when all properties are null', function () {
            $user = new User();
            expect($user->jsonSerialize())->toBe([]);
        });

        it('serializes with object as stdClass', function () {
            $obj = new stdClass();
            $obj->name = 'test';
            $obj->value = 123;
            $user = new User(object: $obj);
            expect($user->jsonSerialize())->toBe(['object' => ['name' => 'test', 'value' => 123]]);
        });

        it('serializes with object as DummyJsonSerializable', function () {
            $obj = new DummyJsonSerializable();
            $user = new User(object: $obj);
            expect($user->jsonSerialize())->toBe(['object' => ['data' => 'serializable']]);
        });

        it('serializes with object as DummyToString', function () {
            $obj = new DummyToString();
            $user = new User(object: $obj);
            // As per User model, if object is not JsonSerializable or stdClass, it's cast to string.
            expect($user->jsonSerialize())->toBe(['object' => 'object_as_string']);
        });

        it('serializes with userset set', function () {
            $userset = new DummyUsersetUser('custom_type', 'custom_id');
            $user = new User(userset: $userset);
            expect($user->jsonSerialize())->toBe(['userset' => ['type' => 'custom_type', 'id' => 'custom_id', 'relation' => null]]);
        });

        it('serializes with wildcard set', function () {
            $wildcard = new DummyTypedWildcard('custom_wildcard');
            $user = new User(wildcard: $wildcard);
            expect($user->jsonSerialize())->toBe(['wildcard' => ['type' => 'custom_wildcard']]);
        });

        it('serializes with all properties set', function () {
            $obj = new DummyJsonSerializable();
            $userset = new DummyUsersetUser();
            $wildcard = new DummyTypedWildcard();
            $user = new User(object: $obj, userset: $userset, wildcard: $wildcard);
            expect($user->jsonSerialize())->toBe([
                'object' => ['data' => 'serializable'],
                'userset' => ['type' => 'userset_type', 'id' => 'dummy_id', 'relation' => null],
                'wildcard' => ['type' => 'wildcard_type'],
            ]);
        });
    });

    describe('static schema()', function () {
        $schema = User::schema();

        it('returns a SchemaInterface instance', function () use ($schema) {
            expect($schema)->toBeInstanceOf(SchemaInterface::class);
        });

        it('has the correct className', function () use ($schema) {
            expect($schema->getClassName())->toBe(User::class);
        });

        it('has "object" property defined correctly', function () use ($schema) {
            $properties = $schema->getProperties();
            expect($properties)->toHaveKey('object');
            $prop = $properties['object'];
            expect($prop->getName())->toBe('object')
                ->and($prop->getTypes())->toBe(['object']) // Based on schema, this is 'object' not a specific class
                ->and($prop->isRequired())->toBeFalse();
        });

        it('has "userset" property defined correctly', function () use ($schema) {
            $properties = $schema->getProperties();
            expect($properties)->toHaveKey('userset');
            $prop = $properties['userset'];
            expect($prop->getName())->toBe('userset')
                ->and($prop->getTypes())->toBe([UsersetUser::class])
                ->and($prop->isRequired())->toBeFalse();
        });

        it('has "wildcard" property defined correctly', function () use ($schema) {
            $properties = $schema->getProperties();
            expect($properties)->toHaveKey('wildcard');
            $prop = $properties['wildcard'];
            expect($prop->getName())->toBe('wildcard')
                ->and($prop->getTypes())->toBe([TypedWildcard::class])
                ->and($prop->isRequired())->toBeFalse();
        });

        it('has "difference" property defined correctly', function () use ($schema) {
            $properties = $schema->getProperties();
            expect($properties)->toHaveKey('difference');
            $prop = $properties['difference'];
            expect($prop->getName())->toBe('difference')
                ->and($prop->getTypes())->toBe([DifferenceV1::class])
                ->and($prop->isRequired())->toBeFalse();
        });
    });
});

?>
