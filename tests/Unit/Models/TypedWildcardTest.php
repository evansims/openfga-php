<?php

declare(strict_types=1);

use OpenFGA\Models\{TypedWildcard, TypedWildcardInterface};
use OpenFGA\Schema\SchemaInterface;

describe('TypedWildcard Model', function (): void {
    test('implements TypedWildcardInterface', function (): void {
        $wildcard = new TypedWildcard(type: 'user');

        expect($wildcard)->toBeInstanceOf(TypedWildcardInterface::class);
    });

    test('constructs with type parameter', function (): void {
        $wildcard = new TypedWildcard(type: 'user');

        expect($wildcard->getType())->toBe('user');
    });

    test('handles various type names', function (): void {
        $types = [
            'user',
            'group',
            'document',
            'folder',
            'organization',
            'service-account',
            'application',
            'team_member',
            'resource:type',
        ];

        foreach ($types as $type) {
            $wildcard = new TypedWildcard(type: $type);
            expect($wildcard->getType())->toBe($type);
        }
    });

    test('serializes to JSON', function (): void {
        $wildcard = new TypedWildcard(type: 'user');

        $json = $wildcard->jsonSerialize();

        expect($json)->toBe(['type' => 'user']);
    });

    test('returns schema instance', function (): void {
        $schema = TypedWildcard::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema->getClassName())->toBe(TypedWildcard::class);

        $properties = $schema->getProperties();
        expect($properties)->toHaveCount(1);

        $propertyNames = array_keys($properties);
        expect($propertyNames)->toBe(['type']);
    });

    test('schema properties have correct configuration', function (): void {
        $schema = TypedWildcard::schema();
        $properties = $schema->getProperties();

        // Type property
        $typeProp = $properties['type'];
        expect($typeProp->name)->toBe('type');
        expect($typeProp->type)->toBe('string');
        expect($typeProp->required)->toBe(true);
    });

    test('schema is cached', function (): void {
        $schema1 = TypedWildcard::schema();
        $schema2 = TypedWildcard::schema();

        expect($schema1)->toBe($schema2);
    });

    test('throws exception for empty string type', function (): void {
        expect(fn () => new TypedWildcard(type: ''))
            ->toThrow(InvalidArgumentException::class, 'TypedWildcard::$type cannot be empty.');
    });

    test('trims and lowercases type', function (): void {
        $wildcard = new TypedWildcard(type: '  USER  ');

        expect($wildcard->getType())->toBe('user');
        expect($wildcard->jsonSerialize())->toBe(['type' => 'user']);
    });

    test('handles unicode characters', function (): void {
        $wildcard = new TypedWildcard(type: 'пользователь');

        expect($wildcard->getType())->toBe('пользователь');
        expect($wildcard->jsonSerialize())->toBe(['type' => 'пользователь']);
    });

    test('handles special characters in type', function (): void {
        $types = [
            'type-with-dash',
            'type_with_underscore',
            'type:with:colon',
            'type.with.dot',
            'type/with/slash',
            'type@with@at',
        ];

        foreach ($types as $type) {
            $wildcard = new TypedWildcard(type: $type);
            expect($wildcard->getType())->toBe($type);
            expect($wildcard->jsonSerialize())->toBe(['type' => $type]);
        }
    });
});
