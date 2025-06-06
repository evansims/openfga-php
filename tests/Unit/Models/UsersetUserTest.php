<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models;

use OpenFGA\Models\{UsersetUser, UsersetUserInterface};
use OpenFGA\Schemas\SchemaInterface;

describe('UsersetUser Model', function (): void {
    test('implements UsersetUserInterface', function (): void {
        $usersetUser = new UsersetUser(type: 'group', id: 'admins', relation: 'member');

        expect($usersetUser)->toBeInstanceOf(UsersetUserInterface::class);
    });

    test('constructs with required parameters', function (): void {
        $usersetUser = new UsersetUser(
            type: 'group',
            id: 'admins',
            relation: 'member',
        );

        expect($usersetUser->getType())->toBe('group');
        expect($usersetUser->getId())->toBe('admins');
        expect($usersetUser->getRelation())->toBe('member');
    });

    test('handles various type formats', function (): void {
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
            $usersetUser = new UsersetUser(
                type: $type,
                id: 'test-id',
                relation: 'viewer',
            );
            expect($usersetUser->getType())->toBe($type);
        }
    });

    test('handles various id formats', function (): void {
        $ids = [
            '123',
            'abc-123',
            'user@example.com',
            'service-account-123',
            'uuid-v4-here',
            'id with spaces',
            'id/with/slashes',
            'id:with:colons',
        ];

        foreach ($ids as $id) {
            $usersetUser = new UsersetUser(
                type: 'user',
                id: $id,
                relation: 'viewer',
            );
            expect($usersetUser->getId())->toBe($id);
        }
    });

    test('handles various relation names', function (): void {
        $relations = [
            'viewer',
            'editor',
            'owner',
            'member',
            'admin',
            'can_view',
            'can_edit',
            'parent',
            'child',
            'relation-with-dash',
            'relation_with_underscore',
        ];

        foreach ($relations as $relation) {
            $usersetUser = new UsersetUser(
                type: 'user',
                id: '123',
                relation: $relation,
            );
            expect($usersetUser->getRelation())->toBe($relation);
        }
    });

    test('serializes to JSON', function (): void {
        $usersetUser = new UsersetUser(
            type: 'group',
            id: 'admins',
            relation: 'member',
        );

        $json = $usersetUser->jsonSerialize();

        expect($json)->toBe([
            'type' => 'group',
            'id' => 'admins',
            'relation' => 'member',
        ]);
    });

    test('returns schema instance', function (): void {
        $schema = UsersetUser::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema->getClassName())->toBe(UsersetUser::class);

        $properties = $schema->getProperties();
        expect($properties)->toHaveCount(3);

        $propertyNames = array_keys($properties);
        expect($propertyNames)->toBe(['type', 'id', 'relation']);
    });

    test('schema properties have correct configuration', function (): void {
        $schema = UsersetUser::schema();
        $properties = $schema->getProperties();

        // Type property
        $typeProp = $properties['type'];
        expect($typeProp->name)->toBe('type');
        expect($typeProp->type)->toBe('string');
        expect($typeProp->required)->toBe(true);

        // ID property
        $idProp = $properties['id'];
        expect($idProp->name)->toBe('id');
        expect($idProp->type)->toBe('string');
        expect($idProp->required)->toBe(true);

        // Relation property
        $relationProp = $properties['relation'];
        expect($relationProp->name)->toBe('relation');
        expect($relationProp->type)->toBe('string');
        expect($relationProp->required)->toBe(true);
    });

    test('schema is cached', function (): void {
        $schema1 = UsersetUser::schema();
        $schema2 = UsersetUser::schema();

        expect($schema1)->toBe($schema2);
    });

    test('preserves empty strings', function (): void {
        $usersetUser = new UsersetUser(
            type: '',
            id: '',
            relation: '',
        );

        expect($usersetUser->getType())->toBe('');
        expect($usersetUser->getId())->toBe('');
        expect($usersetUser->getRelation())->toBe('');
        expect($usersetUser->jsonSerialize())->toBe([
            'type' => '',
            'id' => '',
            'relation' => '',
        ]);
    });

    test('preserves whitespace', function (): void {
        $usersetUser = new UsersetUser(
            type: '  group  ',
            id: '  admins  ',
            relation: '  member  ',
        );

        expect($usersetUser->getType())->toBe('  group  ');
        expect($usersetUser->getId())->toBe('  admins  ');
        expect($usersetUser->getRelation())->toBe('  member  ');
    });

    test('handles unicode characters', function (): void {
        $usersetUser = new UsersetUser(
            type: 'группа',
            id: 'админы',
            relation: 'участник',
        );

        expect($usersetUser->getType())->toBe('группа');
        expect($usersetUser->getId())->toBe('админы');
        expect($usersetUser->getRelation())->toBe('участник');
    });

    test('represents typical userset patterns', function (): void {
        // group:admins#member
        $groupMember = new UsersetUser(
            type: 'group',
            id: 'admins',
            relation: 'member',
        );
        expect($groupMember->jsonSerialize())->toBe([
            'type' => 'group',
            'id' => 'admins',
            'relation' => 'member',
        ]);

        // document:roadmap#viewer
        $documentViewer = new UsersetUser(
            type: 'document',
            id: 'roadmap',
            relation: 'viewer',
        );
        expect($documentViewer->jsonSerialize())->toBe([
            'type' => 'document',
            'id' => 'roadmap',
            'relation' => 'viewer',
        ]);

        // organization:acme#admin
        $orgAdmin = new UsersetUser(
            type: 'organization',
            id: 'acme',
            relation: 'admin',
        );
        expect($orgAdmin->jsonSerialize())->toBe([
            'type' => 'organization',
            'id' => 'acme',
            'relation' => 'admin',
        ]);
    });
});
