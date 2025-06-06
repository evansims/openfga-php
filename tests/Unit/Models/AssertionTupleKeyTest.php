<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models;

use OpenFGA\Models\{AssertionTupleKey, AssertionTupleKeyInterface};
use OpenFGA\Schemas\SchemaInterface;

describe('AssertionTupleKey Model', function (): void {
    test('implements AssertionTupleKeyInterface', function (): void {
        $tupleKey = new AssertionTupleKey(
            user: 'user:anne',
            relation: 'viewer',
            object: 'document:roadmap',
        );

        expect($tupleKey)->toBeInstanceOf(AssertionTupleKeyInterface::class);
    });

    test('constructs with required parameters', function (): void {
        $tupleKey = new AssertionTupleKey(
            user: 'user:anne',
            relation: 'viewer',
            object: 'document:roadmap',
        );

        expect($tupleKey->getUser())->toBe('user:anne');
        expect($tupleKey->getRelation())->toBe('viewer');
        expect($tupleKey->getObject())->toBe('document:roadmap');
    });

    test('handles different user formats', function (): void {
        $formats = [
            'user:anne',
            'group:admins#member',
            'folder:projects#viewer',
            'user:*',
            'application:client-123',
        ];

        foreach ($formats as $userFormat) {
            $tupleKey = new AssertionTupleKey(
                user: $userFormat,
                relation: 'viewer',
                object: 'document:test',
            );

            expect($tupleKey->getUser())->toBe($userFormat);
        }
    });

    test('handles different relation types', function (): void {
        $relations = ['viewer', 'editor', 'owner', 'can_read', 'can_write', 'parent'];

        foreach ($relations as $relation) {
            $tupleKey = new AssertionTupleKey(
                user: 'user:test',
                relation: $relation,
                object: 'document:test',
            );

            expect($tupleKey->getRelation())->toBe($relation);
        }
    });

    test('handles different object formats', function (): void {
        $objects = [
            'document:roadmap',
            'folder:projects',
            'repo:github/openfga',
            'resource:123e4567-e89b-12d3-a456-426614174000',
            'file:/path/to/file.txt',
        ];

        foreach ($objects as $object) {
            $tupleKey = new AssertionTupleKey(
                user: 'user:test',
                relation: 'viewer',
                object: $object,
            );

            expect($tupleKey->getObject())->toBe($object);
        }
    });

    test('serializes to JSON correctly', function (): void {
        $tupleKey = new AssertionTupleKey(
            user: 'user:anne',
            relation: 'viewer',
            object: 'document:roadmap',
        );

        $json = $tupleKey->jsonSerialize();

        expect($json)->toBe([
            'user' => 'user:anne',
            'relation' => 'viewer',
            'object' => 'document:roadmap',
        ]);
    });

    test('handles empty strings', function (): void {
        $tupleKey = new AssertionTupleKey(
            user: '',
            relation: '',
            object: '',
        );

        expect($tupleKey->getUser())->toBe('');
        expect($tupleKey->getRelation())->toBe('');
        expect($tupleKey->getObject())->toBe('');

        $json = $tupleKey->jsonSerialize();
        expect($json)->toBe([
            'user' => '',
            'relation' => '',
            'object' => '',
        ]);
    });

    test('handles special characters in values', function (): void {
        $tupleKey = new AssertionTupleKey(
            user: 'user:anne@example.com',
            relation: 'can_view',
            object: 'document:2023/Q4/report.pdf',
        );

        expect($tupleKey->getUser())->toBe('user:anne@example.com');
        expect($tupleKey->getRelation())->toBe('can_view');
        expect($tupleKey->getObject())->toBe('document:2023/Q4/report.pdf');
    });

    test('returns schema instance', function (): void {
        $schema = AssertionTupleKey::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema->getClassName())->toBe(AssertionTupleKey::class);

        $properties = $schema->getProperties();
        expect($properties)->toHaveCount(3);

        $propertyNames = array_keys($properties);
        expect($propertyNames)->toBe(['user', 'relation', 'object']);

        foreach ($properties as $property) {
            expect($property->type)->toBe('string');
            expect($property->required)->toBe(true);
        }
    });

    test('schema is cached', function (): void {
        $schema1 = AssertionTupleKey::schema();
        $schema2 = AssertionTupleKey::schema();

        expect($schema1)->toBe($schema2);
    });

    test('preserves exact input values without modification', function (): void {
        $tupleKey = new AssertionTupleKey(
            user: '  user:anne  ',
            relation: '  viewer  ',
            object: '  document:roadmap  ',
        );

        expect($tupleKey->getUser())->toBe('  user:anne  ');
        expect($tupleKey->getRelation())->toBe('  viewer  ');
        expect($tupleKey->getObject())->toBe('  document:roadmap  ');
    });
});
