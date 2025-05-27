<?php

declare(strict_types=1);

use OpenFGA\Models\{Node, Leaf, Computed};
use OpenFGA\Models\Collections\{Nodes, NodesInterface};
use OpenFGA\Schema\{SchemaInterface, CollectionSchemaInterface};

describe('Nodes Collection', function (): void {
    test('implements NodesInterface', function (): void {
        $collection = new Nodes();

        expect($collection)->toBeInstanceOf(NodesInterface::class);
    });

    test('constructs with empty array', function (): void {
        $collection = new Nodes();

        expect($collection->count())->toBe(0);
        expect($collection->isEmpty())->toBeTrue();
    });

    test('constructs with array of nodes', function (): void {
        $node1 = new Node(name: 'viewer');
        $node2 = new Node(name: 'editor');
        $node3 = new Node(name: 'owner');
        
        $collection = new Nodes([$node1, $node2, $node3]);

        expect($collection->count())->toBe(3);
        expect($collection->isEmpty())->toBeFalse();
    });

    test('adds nodes', function (): void {
        $collection = new Nodes();
        
        $node = new Node(
            name: 'viewer',
            leaf: new Leaf(computed: new Computed(userset: 'viewer'))
        );
        
        $collection->add($node);
        
        expect($collection->count())->toBe(1);
        expect($collection->get(0))->toBe($node);
    });

    test('checks if node exists', function (): void {
        $node = new Node(name: 'admin');
        $collection = new Nodes([$node]);
        
        expect(isset($collection[0]))->toBeTrue();
        expect(isset($collection[1]))->toBeFalse();
    });

    test('iterates over nodes', function (): void {
        $node1 = new Node(name: 'read');
        $node2 = new Node(name: 'write');
        $node3 = new Node(name: 'delete');
        
        $collection = new Nodes([$node1, $node2, $node3]);
        
        $names = [];
        foreach ($collection as $node) {
            $names[] = $node->getName();
        }
        
        expect($names)->toBe(['read', 'write', 'delete']);
    });

    test('converts to array', function (): void {
        $node1 = new Node(name: 'user');
        $node2 = new Node(name: 'admin');
        
        $collection = new Nodes([$node1, $node2]);
        $array = $collection->toArray();
        
        expect($array)->toBeArray();
        expect($array)->toHaveCount(2);
        expect($array[0])->toBe($node1);
        expect($array[1])->toBe($node2);
    });

    test('serializes to JSON', function (): void {
        $node1 = new Node(name: 'viewer');
        $node2 = new Node(
            name: 'editor',
            leaf: new Leaf(computed: new Computed(userset: 'editor'))
        );
        
        $collection = new Nodes([$node1, $node2]);
        $json = $collection->jsonSerialize();
        
        expect($json)->toBeArray();
        expect($json)->toHaveCount(2);
        expect($json[0])->toBe(['name' => 'viewer']);
        expect($json[1])->toBe([
            'name' => 'editor',
            'leaf' => [
                'computed' => ['userset' => 'editor'],
            ],
        ]);
    });

    test('returns schema instance', function (): void {
        $schema = Nodes::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema)->toBeInstanceOf(CollectionSchemaInterface::class);
        expect($schema->getClassName())->toBe(Nodes::class);
    });

    test('schema is cached', function (): void {
        $schema1 = Nodes::schema();
        $schema2 = Nodes::schema();

        expect($schema1)->toBeInstanceOf(CollectionSchemaInterface::class);
        expect($schema2)->toBeInstanceOf(CollectionSchemaInterface::class);
    });

    test('handles complex node hierarchies', function (): void {
        // Create a permission hierarchy
        $ownerNode = new Node(
            name: 'owner',
            leaf: new Leaf(computed: new Computed(userset: 'owner'))
        );
        
        $editorNode = new Node(
            name: 'editor',
            leaf: new Leaf(computed: new Computed(userset: 'editor')),
            union: $ownerNode
        );
        
        $viewerNode = new Node(
            name: 'viewer',
            leaf: new Leaf(computed: new Computed(userset: 'viewer')),
            union: $editorNode
        );
        
        $collection = new Nodes([$viewerNode, $editorNode, $ownerNode]);
        
        expect($collection->count())->toBe(3);
        
        // Verify hierarchy
        $viewer = $collection->get(0);
        expect($viewer->getName())->toBe('viewer');
        expect($viewer->getUnion())->toBe($editorNode);
        
        $editor = $collection->get(1);
        expect($editor->getName())->toBe('editor');
        expect($editor->getUnion())->toBe($ownerNode);
    });

    test('supports building authorization trees', function (): void {
        // Build a typical RBAC tree
        $permissions = new Nodes([
            new Node(name: 'create', leaf: new Leaf(computed: new Computed(userset: 'creator'))),
            new Node(name: 'read', leaf: new Leaf(computed: new Computed(userset: 'reader'))),
            new Node(name: 'update', leaf: new Leaf(computed: new Computed(userset: 'editor'))),
            new Node(name: 'delete', leaf: new Leaf(computed: new Computed(userset: 'owner'))),
        ]);
        
        expect($permissions->count())->toBe(4);
        
        $permissionNames = [];
        foreach ($permissions as $perm) {
            $permissionNames[] = $perm->getName();
        }
        
        expect($permissionNames)->toBe(['create', 'read', 'update', 'delete']);
    });

    test('filters nodes by criteria', function (): void {
        $collection = new Nodes([
            new Node(name: 'public_read'),
            new Node(name: 'member_write', leaf: new Leaf(computed: new Computed(userset: 'member'))),
            new Node(name: 'admin_delete', leaf: new Leaf(computed: new Computed(userset: 'admin'))),
            new Node(name: 'owner_all', leaf: new Leaf(computed: new Computed(userset: 'owner'))),
        ]);
        
        // Filter nodes that have leaves
        $nodesWithLeaves = [];
        foreach ($collection as $node) {
            if ($node->getLeaf() !== null) {
                $nodesWithLeaves[] = $node->getName();
            }
        }
        
        expect($nodesWithLeaves)->toBe(['member_write', 'admin_delete', 'owner_all']);
    });

    test('handles empty collection edge cases', function (): void {
        $collection = new Nodes();
        
        expect($collection->isEmpty())->toBeTrue();
        expect($collection->toArray())->toBe([]);
        expect($collection->jsonSerialize())->toBe([]);
        
        // Test iteration on empty collection
        $count = 0;
        foreach ($collection as $item) {
            $count++;
        }
        expect($count)->toBe(0);
        
        // Test get on empty collection
        expect($collection->get(0))->toBeNull();
    });
});