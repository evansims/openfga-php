<?php

declare(strict_types=1);

use OpenFGA\Models\Collections\{Nodes, UsersList};
use OpenFGA\Models\{Leaf, Node, NodeUnion, NodeUnionInterface};
use OpenFGA\Schema\SchemaInterface;

describe('NodeUnion Model', function (): void {
    test('implements NodeUnionInterface', function (): void {
        $nodes = [];
        $nodeUnion = new NodeUnion(nodes: $nodes);

        expect($nodeUnion)->toBeInstanceOf(NodeUnionInterface::class);
    });

    test('constructs with empty nodes', function (): void {
        $nodes = [];
        $nodeUnion = new NodeUnion(nodes: $nodes);

        expect($nodeUnion->getNodes())->toBe($nodes);
        expect(\count($nodeUnion->getNodes()))->toBe(0);
    });

    test('constructs with single node', function (): void {
        $leaf = new Leaf(users: new UsersList());
        $node = new Node(name: 'document:1#viewer', leaf: $leaf);
        $nodes = [$node];

        $nodeUnion = new NodeUnion(nodes: $nodes);

        expect($nodeUnion->getNodes())->toBe($nodes);
        expect(\count($nodeUnion->getNodes()))->toBe(1);
        expect($nodeUnion->getNodes()[0])->toBe($node);
    });

    test('constructs with multiple nodes', function (): void {
        $node1 = new Node(name: 'document:1#viewer', leaf: new Leaf(users: new UsersList()));
        $node2 = new Node(name: 'document:2#editor', leaf: new Leaf(users: new UsersList()));
        $node3 = new Node(name: 'document:3#owner', leaf: new Leaf(users: new UsersList()));
        $nodes = [$node1, $node2, $node3];

        $nodeUnion = new NodeUnion(nodes: $nodes);

        expect($nodeUnion->getNodes())->toBe($nodes);
        expect(\count($nodeUnion->getNodes()))->toBe(3);
        expect($nodeUnion->getNodes())->toBe([$node1, $node2, $node3]);
    });

    test('serializes to JSON', function (): void {
        $node1 = new Node(name: 'document:1#viewer', leaf: new Leaf(users: new UsersList()));
        $node2 = new Node(name: 'document:2#editor', leaf: new Leaf(users: new UsersList()));
        $nodes = [$node1, $node2];

        $nodeUnion = new NodeUnion(nodes: $nodes);
        $json = $nodeUnion->jsonSerialize();

        expect($json)->toBeArray();
        expect($json)->toHaveKey('nodes');
        expect($json['nodes'])->toBeArray();
        expect($json['nodes'])->toHaveCount(2);
        expect($json['nodes'][0])->toBe($node1->jsonSerialize());
        expect($json['nodes'][1])->toBe($node2->jsonSerialize());
    });

    test('serializes with empty nodes', function (): void {
        $nodes = [];
        $nodeUnion = new NodeUnion(nodes: $nodes);
        $json = $nodeUnion->jsonSerialize();

        expect($json)->toBe(['nodes' => []]);
    });

    test('returns schema instance', function (): void {
        $schema = NodeUnion::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema->getClassName())->toBe(NodeUnion::class);

        $properties = $schema->getProperties();
        expect($properties)->toHaveCount(1);

        $nodesProperty = $properties['nodes'] ?? null;
        expect($nodesProperty)->not->toBeNull();
        expect($nodesProperty->name)->toBe('nodes');
        expect($nodesProperty->required)->toBe(true);
        expect($nodesProperty->type)->toBe('array');
    });

    test('schema is cached', function (): void {
        $schema1 = NodeUnion::schema();
        $schema2 = NodeUnion::schema();

        expect($schema1)->toBe($schema2);
    });

    test('has correct OpenAPI type constant', function (): void {
        expect(NodeUnion::OPENAPI_TYPE)->toBe('NodeUnion');
    });

    test('handles complex node structures', function (): void {
        // Create nested node structure with union
        $leaf1 = new Leaf(users: new UsersList());
        $leaf2 = new Leaf(users: new UsersList());

        $childNode1 = new Node(name: 'group:admins#member', leaf: $leaf1);
        $childNode2 = new Node(name: 'group:editors#member', leaf: $leaf2);
        $childNodes = [$childNode1, $childNode2];
        $childUnion = new NodeUnion(nodes: $childNodes);

        $parentNode = new Node(name: 'document:1#viewer', union: $childUnion);
        $parentNodes = [$parentNode];

        $nodeUnion = new NodeUnion(nodes: $parentNodes);

        expect(\count($nodeUnion->getNodes()))->toBe(1);
        expect($nodeUnion->getNodes()[0]->getName())->toBe('document:1#viewer');
        expect($nodeUnion->getNodes()[0]->getUnion())->toBe($childUnion);
    });

    test('maintains immutability', function (): void {
        $originalNodes = [];
        $nodeUnion = new NodeUnion(nodes: $originalNodes);

        // Get the nodes and try to modify them
        $retrievedNodes = $nodeUnion->getNodes();

        // The retrieved nodes should be the same array
        expect($retrievedNodes)->toBe($originalNodes);

        // Since arrays are passed by value, modifying the original doesn't affect the union
        $newNode = new Node(name: 'test:node', leaf: new Leaf(users: new UsersList()));
        $originalNodes[] = $newNode;

        // The union still has the original empty array
        expect($nodeUnion->getNodes())->toBe([]);
        expect(\count($nodeUnion->getNodes()))->toBe(0);
    });
});
