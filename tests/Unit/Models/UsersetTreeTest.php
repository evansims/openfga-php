<?php

declare(strict_types=1);

use OpenFGA\Models\{UsersetTree, UsersetTreeInterface, Node, Leaf, Computed};
use OpenFGA\Schema\SchemaInterface;

describe('UsersetTree Model', function (): void {
    test('implements UsersetTreeInterface', function (): void {
        $root = new Node(name: 'viewer');
        $usersetTree = new UsersetTree(root: $root);

        expect($usersetTree)->toBeInstanceOf(UsersetTreeInterface::class);
    });

    test('constructs with root node', function (): void {
        $root = new Node(
            name: 'viewer',
            leaf: new Leaf(computed: new Computed(userset: 'viewer'))
        );
        
        $usersetTree = new UsersetTree(root: $root);

        expect($usersetTree->getRoot())->toBe($root);
    });

    test('serializes to JSON', function (): void {
        $root = new Node(
            name: 'viewer',
            leaf: new Leaf(computed: new Computed(userset: 'viewer'))
        );
        
        $usersetTree = new UsersetTree(root: $root);
        
        expect($usersetTree->jsonSerialize())->toBe([
            'root' => [
                'name' => 'viewer',
                'leaf' => [
                    'computed' => ['userset' => 'viewer'],
                ],
            ],
        ]);
    });

    test('handles complex tree structure', function (): void {
        // Create owner node
        $ownerNode = new Node(
            name: 'owner',
            leaf: new Leaf(computed: new Computed(userset: 'owner'))
        );
        
        // Create editor node
        $editorNode = new Node(
            name: 'editor',
            leaf: new Leaf(computed: new Computed(userset: 'editor'))
        );
        
        // Create viewer node with union of owner
        $viewerNode = new Node(
            name: 'viewer',
            union: $ownerNode
        );
        
        $usersetTree = new UsersetTree(root: $viewerNode);
        
        $json = $usersetTree->jsonSerialize();
        expect($json)->toHaveKey('root');
        expect($json['root']['name'])->toBe('viewer');
        expect($json['root'])->toHaveKey('union');
        expect($json['root']['union']['name'])->toBe('owner');
    });

    test('returns schema instance', function (): void {
        $schema = UsersetTree::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema->getClassName())->toBe(UsersetTree::class);

        $properties = $schema->getProperties();
        expect($properties)->toHaveCount(1);

        $propertyNames = array_keys($properties);
        expect($propertyNames)->toBe(['root']);
    });

    test('schema properties have correct configuration', function (): void {
        $schema = UsersetTree::schema();
        $properties = $schema->getProperties();

        // Root property
        $rootProp = $properties['root'];
        expect($rootProp->name)->toBe('root');
        expect($rootProp->type)->toBe(Node::class);
        expect($rootProp->required)->toBe(true);
    });

    test('schema is cached', function (): void {
        $schema1 = UsersetTree::schema();
        $schema2 = UsersetTree::schema();

        expect($schema1)->toBe($schema2);
    });

    test('handles typical authorization patterns', function (): void {
        // Pattern 1: Simple direct relation
        $directRoot = new Node(
            name: 'member',
            leaf: new Leaf(computed: new Computed(userset: 'member'))
        );
        $directTree = new UsersetTree(root: $directRoot);
        
        expect($directTree->jsonSerialize())->toBe([
            'root' => [
                'name' => 'member',
                'leaf' => [
                    'computed' => ['userset' => 'member'],
                ],
            ],
        ]);
        
        // Pattern 2: Hierarchical permissions
        $ownerLeaf = new Leaf(computed: new Computed(userset: 'owner'));
        $ownerNode = new Node(name: 'owner', leaf: $ownerLeaf);
        
        $editorLeaf = new Leaf(computed: new Computed(userset: 'editor'));
        $editorNode = new Node(name: 'editor', leaf: $editorLeaf, union: $ownerNode);
        
        $viewerLeaf = new Leaf(computed: new Computed(userset: 'viewer'));
        $viewerNode = new Node(name: 'viewer', leaf: $viewerLeaf, union: $editorNode);
        
        $hierarchicalTree = new UsersetTree(root: $viewerNode);
        
        $json = $hierarchicalTree->jsonSerialize();
        expect($json['root']['name'])->toBe('viewer');
        expect($json['root'])->toHaveKey('leaf');
        expect($json['root'])->toHaveKey('union');
        expect($json['root']['union']['name'])->toBe('editor');
        
        // Pattern 3: Complex tree with difference
        $allUsersNode = new Node(
            name: 'all_users',
            leaf: new Leaf(computed: new Computed(userset: 'user:*'))
        );
        $blockedNode = new Node(
            name: 'blocked',
            leaf: new Leaf(computed: new Computed(userset: 'blocked'))
        );
        $allowedNode = new Node(
            name: 'allowed',
            difference: new OpenFGA\Models\UsersetTreeDifference(
                base: $allUsersNode,
                subtract: $blockedNode
            )
        );
        
        $complexTree = new UsersetTree(root: $allowedNode);
        
        $json3 = $complexTree->jsonSerialize();
        expect($json3['root']['name'])->toBe('allowed');
        expect($json3['root'])->toHaveKey('difference');
    });
});