<?php

declare(strict_types=1);

use OpenFGA\Models\{Computed, Leaf, Node, UsersetTreeDifference, UsersetTreeDifferenceInterface};
use OpenFGA\Schema\SchemaInterface;

describe('UsersetTreeDifference Model', function (): void {
    test('implements UsersetTreeDifferenceInterface', function (): void {
        $base = new Node(name: 'all_users');
        $subtract = new Node(name: 'blocked_users');
        $difference = new UsersetTreeDifference(base: $base, subtract: $subtract);

        expect($difference)->toBeInstanceOf(UsersetTreeDifferenceInterface::class);
    });

    test('constructs with base and subtract nodes', function (): void {
        $base = new Node(
            name: 'all_users',
            leaf: new Leaf(computed: new Computed(userset: 'user:*')),
        );
        $subtract = new Node(
            name: 'blocked_users',
            leaf: new Leaf(computed: new Computed(userset: 'blocked')),
        );

        $difference = new UsersetTreeDifference(base: $base, subtract: $subtract);

        expect($difference->getBase())->toBe($base);
        expect($difference->getSubtract())->toBe($subtract);
    });

    test('serializes to JSON', function (): void {
        $base = new Node(
            name: 'all_users',
            leaf: new Leaf(computed: new Computed(userset: 'user:*')),
        );
        $subtract = new Node(
            name: 'blocked_users',
            leaf: new Leaf(computed: new Computed(userset: 'blocked')),
        );

        $difference = new UsersetTreeDifference(base: $base, subtract: $subtract);

        expect($difference->jsonSerialize())->toBe([
            'base' => [
                'name' => 'all_users',
                'leaf' => [
                    'computed' => ['userset' => 'user:*'],
                ],
            ],
            'subtract' => [
                'name' => 'blocked_users',
                'leaf' => [
                    'computed' => ['userset' => 'blocked'],
                ],
            ],
        ]);
    });

    test('handles complex nested differences', function (): void {
        // Base: users who are owners OR editors
        $ownerNode = new Node(
            name: 'owner',
            leaf: new Leaf(computed: new Computed(userset: 'owner')),
        );
        $editorNode = new Node(
            name: 'editor',
            leaf: new Leaf(computed: new Computed(userset: 'editor')),
        );
        $baseNode = new Node(
            name: 'owner_or_editor',
            union: $ownerNode,
        );

        // Subtract: suspended users
        $suspendedNode = new Node(
            name: 'suspended',
            leaf: new Leaf(computed: new Computed(userset: 'suspended')),
        );

        $difference = new UsersetTreeDifference(base: $baseNode, subtract: $suspendedNode);

        $json = $difference->jsonSerialize();
        expect($json['base']['name'])->toBe('owner_or_editor');
        expect($json['base'])->toHaveKey('union');
        expect($json['subtract']['name'])->toBe('suspended');
    });

    test('returns schema instance', function (): void {
        $schema = UsersetTreeDifference::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema->getClassName())->toBe(UsersetTreeDifference::class);

        $properties = $schema->getProperties();
        expect($properties)->toHaveCount(2);

        $propertyNames = array_keys($properties);
        expect($propertyNames)->toBe(['base', 'subtract']);
    });

    test('schema properties have correct configuration', function (): void {
        $schema = UsersetTreeDifference::schema();
        $properties = $schema->getProperties();

        // Base property
        $baseProp = $properties['base'];
        expect($baseProp->name)->toBe('base');
        expect($baseProp->type)->toBe('object');
        expect($baseProp->required)->toBe(true);

        // Subtract property
        $subtractProp = $properties['subtract'];
        expect($subtractProp->name)->toBe('subtract');
        expect($subtractProp->type)->toBe('object');
        expect($subtractProp->required)->toBe(true);
    });

    test('schema is cached', function (): void {
        $schema1 = UsersetTreeDifference::schema();
        $schema2 = UsersetTreeDifference::schema();

        expect($schema1)->toBe($schema2);
    });

    test('handles typical authorization patterns', function (): void {
        // Pattern 1: All users except blocked
        $allUsersNode = new Node(
            name: 'all_users',
            leaf: new Leaf(computed: new Computed(userset: 'user:*')),
        );
        $blockedNode = new Node(
            name: 'blocked',
            leaf: new Leaf(computed: new Computed(userset: 'blocked')),
        );
        $activeUsersDiff = new UsersetTreeDifference(base: $allUsersNode, subtract: $blockedNode);

        $json = $activeUsersDiff->jsonSerialize();
        expect($json['base']['leaf']['computed']['userset'])->toBe('user:*');
        expect($json['subtract']['leaf']['computed']['userset'])->toBe('blocked');

        // Pattern 2: Members except those in restricted group
        $membersNode = new Node(
            name: 'members',
            leaf: new Leaf(computed: new Computed(userset: 'group:all_members#member')),
        );
        $restrictedNode = new Node(
            name: 'restricted',
            leaf: new Leaf(computed: new Computed(userset: 'group:restricted#member')),
        );
        $allowedMembersDiff = new UsersetTreeDifference(base: $membersNode, subtract: $restrictedNode);

        $json2 = $allowedMembersDiff->jsonSerialize();
        expect($json2['base']['name'])->toBe('members');
        expect($json2['subtract']['name'])->toBe('restricted');

        // Pattern 3: Complex difference with nested structures
        // Base: viewers (which includes owners and editors)
        $viewerOwnerNode = new Node(
            name: 'owner',
            leaf: new Leaf(computed: new Computed(userset: 'owner')),
        );
        $viewerEditorNode = new Node(
            name: 'editor',
            leaf: new Leaf(computed: new Computed(userset: 'editor')),
        );
        $viewersNode = new Node(
            name: 'viewers',
            union: $viewerOwnerNode,
        );

        // Subtract: users in review_required status
        $reviewRequiredNode = new Node(
            name: 'review_required',
            leaf: new Leaf(computed: new Computed(userset: 'review_required')),
        );

        $approvedViewersDiff = new UsersetTreeDifference(
            base: $viewersNode,
            subtract: $reviewRequiredNode,
        );

        $json3 = $approvedViewersDiff->jsonSerialize();
        expect($json3['base']['name'])->toBe('viewers');
        expect($json3['base'])->toHaveKey('union');
        expect($json3['subtract']['name'])->toBe('review_required');
    });
});
