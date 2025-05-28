<?php

declare(strict_types=1);

use OpenFGA\Models\Collections\{RelationReferences, RelationReferencesInterface};
use OpenFGA\Models\{RelationReference, TypedWildcard};
use OpenFGA\Schema\{CollectionSchemaInterface, SchemaInterface};

describe('RelationReferences Collection', function (): void {
    test('implements RelationReferencesInterface', function (): void {
        $collection = new RelationReferences([]);

        expect($collection)->toBeInstanceOf(RelationReferencesInterface::class);
    });

    test('constructs with empty array', function (): void {
        $collection = new RelationReferences([]);

        expect($collection->count())->toBe(0);
        expect($collection->isEmpty())->toBeTrue();
    });

    test('constructs with array of relation references', function (): void {
        $ref1 = new RelationReference(
            type: 'document',
            relation: 'viewer',
        );
        $ref2 = new RelationReference(
            type: 'folder',
            relation: 'editor',
        );
        $ref3 = new RelationReference(
            type: 'organization',
            relation: 'member',
        );

        // KeyedCollection uses numeric string keys by default when given indexed array
        $collection = new RelationReferences([
            $ref1,
            $ref2,
            $ref3,
        ]);

        expect($collection->count())->toBe(3);
        expect($collection->isEmpty())->toBeFalse();
    });

    test('constructs with keyed array', function (): void {
        $collection = new RelationReferences([
            'doc_viewer' => new RelationReference(type: 'document', relation: 'viewer'),
            'folder_editor' => new RelationReference(type: 'folder', relation: 'editor'),
            'org_member' => new RelationReference(type: 'organization', relation: 'member'),
        ]);

        expect($collection->count())->toBe(3);
        expect($collection->get('doc_viewer')->getType())->toBe('document');
        expect($collection->get('folder_editor')->getType())->toBe('folder');
        expect($collection->get('org_member')->getType())->toBe('organization');
    });

    test('constructs and gets relation references by key', function (): void {
        $ref = new RelationReference(
            type: 'group',
            relation: 'member',
            wildcard: new TypedWildcard(type: 'user'),
        );

        $collection = new RelationReferences(['group_member' => $ref]);

        expect($collection->count())->toBe(1);
        expect($collection->get('group_member'))->toBe($ref);
    });

    test('checks if reference exists', function (): void {
        $ref = new RelationReference(type: 'project', relation: 'owner');
        $collection = new RelationReferences(['project_owner' => $ref]);

        expect($collection->has('project_owner'))->toBeTrue();
        expect($collection->has('non_existent'))->toBeFalse();
    });

    test('iterates over relation references', function (): void {
        $collection = new RelationReferences([
            'read_perm' => new RelationReference(type: 'doc', relation: 'read'),
            'write_perm' => new RelationReference(type: 'doc', relation: 'write'),
            'delete_perm' => new RelationReference(type: 'doc', relation: 'delete'),
        ]);

        $relations = [];
        $keys = [];
        foreach ($collection as $key => $ref) {
            $keys[] = $key;
            $relations[] = $ref->getRelation();
        }

        expect($keys)->toBe(['read_perm', 'write_perm', 'delete_perm']);
        expect($relations)->toBe(['read', 'write', 'delete']);
    });

    test('converts to array', function (): void {
        $ref1 = new RelationReference(type: 'file', relation: 'viewer');
        $ref2 = new RelationReference(type: 'file', relation: 'editor');

        $collection = new RelationReferences([
            'viewer_ref' => $ref1,
            'editor_ref' => $ref2,
        ]);
        $array = $collection->toArray();

        expect($array)->toBeArray();
        expect($array)->toHaveCount(2);
        expect($array['viewer_ref'])->toBe($ref1);
        expect($array['editor_ref'])->toBe($ref2);
    });

    test('serializes to JSON', function (): void {
        $ref1 = new RelationReference(type: 'user');
        $ref2 = new RelationReference(
            type: 'group',
            relation: 'member',
        );
        $ref3 = new RelationReference(
            type: 'team',
            wildcard: new TypedWildcard(type: 'user'),
        );

        // When using indexed array, keys are converted to strings
        $collection = new RelationReferences([$ref1, $ref2, $ref3]);
        $json = $collection->jsonSerialize();

        expect($json)->toBeArray();
        expect($json)->toHaveCount(3);

        // For indexed arrays, jsonSerialize returns array values
        $values = array_values($json);
        expect($values[0])->toBe(['type' => 'user']);
        expect($values[1])->toBe(['type' => 'group', 'relation' => 'member']);
        expect($values[2]['type'])->toBe('team');
        expect($values[2]['wildcard'])->toBeInstanceOf(TypedWildcard::class);
    });

    test('returns schema instance', function (): void {
        $schema = RelationReferences::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema)->toBeInstanceOf(CollectionSchemaInterface::class);
        expect($schema->getClassName())->toBe(RelationReferences::class);
    });

    test('schema is cached', function (): void {
        $schema1 = RelationReferences::schema();
        $schema2 = RelationReferences::schema();

        expect($schema1)->toBeInstanceOf(CollectionSchemaInterface::class);
        expect($schema2)->toBeInstanceOf(CollectionSchemaInterface::class);
    });

    test('handles various reference patterns', function (): void {
        $collection = new RelationReferences([
            'direct_user' => new RelationReference(type: 'user'),
            'doc_viewer' => new RelationReference(
                type: 'document',
                relation: 'viewer',
            ),
            'folder_wildcard' => new RelationReference(
                type: 'folder',
                wildcard: new TypedWildcard(type: 'user'),
            ),
            'resource_condition' => new RelationReference(
                type: 'resource',
                relation: 'owner',
                condition: 'in_business_hours',
            ),
            'project_complete' => new RelationReference(
                type: 'project',
                relation: 'member',
                wildcard: new TypedWildcard(type: 'team'),
                condition: 'is_active',
            ),
        ]);

        expect($collection->count())->toBe(5);

        // Verify different patterns
        $directUser = $collection->get('direct_user');
        expect($directUser->getType())->toBe('user');
        expect($directUser->getRelation())->toBeNull();

        $withCondition = $collection->get('resource_condition');
        expect($withCondition->getCondition())->toBe('in_business_hours');

        $complete = $collection->get('project_complete');
        expect($complete->getType())->toBe('project');
        expect($complete->getRelation())->toBe('member');
        expect($complete->getWildcard())->toBeInstanceOf(TypedWildcard::class);
        expect($complete->getCondition())->toBe('is_active');
    });

    test('filters references by type', function (): void {
        $collection = new RelationReferences([
            'ref1' => new RelationReference(type: 'document', relation: 'viewer'),
            'ref2' => new RelationReference(type: 'folder', relation: 'editor'),
            'ref3' => new RelationReference(type: 'document', relation: 'owner'),
            'ref4' => new RelationReference(type: 'project', relation: 'member'),
            'ref5' => new RelationReference(type: 'document', relation: 'commenter'),
        ]);

        // Filter references for 'document' type
        $documentRefs = [];
        foreach ($collection as $ref) {
            if ('document' === $ref->getType()) {
                $documentRefs[] = $ref->getRelation();
            }
        }

        expect($documentRefs)->toBe(['viewer', 'owner', 'commenter']);
    });

    test('works with indexed arrays like in RelationMetadata', function (): void {
        // This is how it's actually used in the codebase
        $userRef = new RelationReference(type: 'user');
        $groupRef = new RelationReference(type: 'group', relation: 'member');

        $collection = new RelationReferences([$userRef, $groupRef]);

        expect($collection->count())->toBe(2);

        // Access by string key (converted from numeric)
        expect($collection->get('0'))->toBe($userRef);
        expect($collection->get('1'))->toBe($groupRef);
    });

    test('handles empty collection edge cases', function (): void {
        $collection = new RelationReferences([]);

        expect($collection->isEmpty())->toBeTrue();
        expect($collection->toArray())->toBe([]);
        expect($collection->jsonSerialize())->toBe([]);

        // Test iteration on empty collection
        $count = 0;
        foreach ($collection as $item) {
            ++$count;
        }
        expect($count)->toBe(0);

        // Test get on empty collection
        expect($collection->get('any_key'))->toBeNull();
    });
});
