<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models;

use OpenFGA\Models\{Computed, Leaf, Node, NodeInterface, NodeUnion, UsersetTreeDifference};
use OpenFGA\Schemas\SchemaInterface;

describe('Node Model', function (): void {
    test('implements NodeInterface', function (): void {
        $node = new Node(name: 'viewer');

        expect($node)->toBeInstanceOf(NodeInterface::class);
    });

    test('constructs with name only', function (): void {
        $node = new Node(name: 'viewer');

        expect($node->getName())->toBe('viewer');
        expect($node->getLeaf())->toBeNull();
        expect($node->getDifference())->toBeNull();
        expect($node->getUnion())->toBeNull();
        expect($node->getIntersection())->toBeNull();
    });

    test('constructs with name and leaf', function (): void {
        $leaf = new Leaf(
            computed: new Computed(userset: 'owner'),
        );
        $node = new Node(name: 'viewer', leaf: $leaf);

        expect($node->getName())->toBe('viewer');
        expect($node->getLeaf())->toBe($leaf);
        expect($node->getDifference())->toBeNull();
        expect($node->getUnion())->toBeNull();
        expect($node->getIntersection())->toBeNull();
    });

    test('constructs with name and difference', function (): void {
        $baseNode = new Node(name: 'all_users');
        $subtractNode = new Node(name: 'blocked_users');
        $difference = new UsersetTreeDifference(base: $baseNode, subtract: $subtractNode);

        $node = new Node(name: 'allowed_users', difference: $difference);

        expect($node->getName())->toBe('allowed_users');
        expect($node->getLeaf())->toBeNull();
        expect($node->getDifference())->toBe($difference);
        expect($node->getUnion())->toBeNull();
        expect($node->getIntersection())->toBeNull();
    });

    test('constructs with name and union', function (): void {
        $unionNode = new Node(name: 'owner_or_editor');

        $node = new Node(name: 'viewer', union: $unionNode);

        expect($node->getName())->toBe('viewer');
        expect($node->getLeaf())->toBeNull();
        expect($node->getDifference())->toBeNull();
        expect($node->getUnion())->toBe($unionNode);
        expect($node->getIntersection())->toBeNull();
    });

    test('constructs with name and intersection', function (): void {
        $intersectionNode = new Node(name: 'member_and_verified');

        $node = new Node(name: 'verified_member', intersection: $intersectionNode);

        expect($node->getName())->toBe('verified_member');
        expect($node->getLeaf())->toBeNull();
        expect($node->getDifference())->toBeNull();
        expect($node->getUnion())->toBeNull();
        expect($node->getIntersection())->toBe($intersectionNode);
    });

    test('serializes to JSON with only non-null fields', function (): void {
        $node = new Node(name: 'viewer');
        expect($node->jsonSerialize())->toBe(['name' => 'viewer']);

        $leaf = new Leaf(
            computed: new Computed(userset: 'owner'),
        );
        $node = new Node(name: 'viewer', leaf: $leaf);
        expect($node->jsonSerialize())->toBe([
            'name' => 'viewer',
            'leaf' => [
                'computed' => ['userset' => 'owner'],
            ],
        ]);

        $unionNode = new Node(name: 'owner_or_editor');
        $node = new Node(name: 'viewer', union: $unionNode);

        expect($node->jsonSerialize())->toBe([
            'name' => 'viewer',
            'union' => ['name' => 'owner_or_editor'],
        ]);
    });

    test('returns schema instance', function (): void {
        $schema = Node::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema->getClassName())->toBe(Node::class);

        $properties = $schema->getProperties();
        expect($properties)->toHaveCount(5);

        $propertyNames = array_keys($properties);
        expect($propertyNames)->toBe(['name', 'leaf', 'difference', 'union', 'intersection']);
    });

    test('schema properties have correct configuration', function (): void {
        $schema = Node::schema();
        $properties = $schema->getProperties();

        $nameProp = $properties['name'];
        expect($nameProp->name)->toBe('name');
        expect($nameProp->type)->toBe('string');
        expect($nameProp->required)->toBe(true);

        $leafProp = $properties['leaf'];
        expect($leafProp->name)->toBe('leaf');
        expect($leafProp->type)->toBe('object');
        expect($leafProp->required)->toBe(false);

        $differenceProp = $properties['difference'];
        expect($differenceProp->name)->toBe('difference');
        expect($differenceProp->type)->toBe('object');
        expect($differenceProp->required)->toBe(false);

        $unionProp = $properties['union'];
        expect($unionProp->name)->toBe('union');
        expect($unionProp->type)->toBe('object');
        expect($unionProp->required)->toBe(false);
        expect($unionProp->className)->toBe(NodeUnion::class);

        $intersectionProp = $properties['intersection'];
        expect($intersectionProp->name)->toBe('intersection');
        expect($intersectionProp->type)->toBe('object');
        expect($intersectionProp->required)->toBe(false);
        expect($intersectionProp->className)->toBe(NodeUnion::class);
    });

    test('schema is cached', function (): void {
        $schema1 = Node::schema();
        $schema2 = Node::schema();

        expect($schema1)->toBe($schema2);
    });

    test('handles typical authorization patterns', function (): void {
        $simpleNode = new Node(
            name: 'owner',
            leaf: new Leaf(
                computed: new Computed(userset: 'owner'),
            ),
        );

        expect($simpleNode->jsonSerialize())->toBe([
            'name' => 'owner',
            'leaf' => [
                'computed' => ['userset' => 'owner'],
            ],
        ]);

        $unionNode = new Node(
            name: 'owner_or_editor',
            leaf: new Leaf(computed: new Computed(userset: 'owner')),
        );
        $viewerNode = new Node(
            name: 'viewer',
            union: $unionNode,
        );

        expect($viewerNode->jsonSerialize())->toBe([
            'name' => 'viewer',
            'union' => [
                'name' => 'owner_or_editor',
                'leaf' => [
                    'computed' => ['userset' => 'owner'],
                ],
            ],
        ]);

        $allUsersNode = new Node(
            name: 'all_users',
            leaf: new Leaf(computed: new Computed(userset: 'user:*')),
        );
        $blockedNode = new Node(
            name: 'blocked_users',
            leaf: new Leaf(computed: new Computed(userset: 'blocked')),
        );
        $allowedNode = new Node(
            name: 'allowed_users',
            difference: new UsersetTreeDifference(base: $allUsersNode, subtract: $blockedNode),
        );

        $json3 = $allowedNode->jsonSerialize();
        expect($json3['name'])->toBe('allowed_users');
        expect($json3)->toHaveKey('difference');
        expect($json3['difference'])->toHaveKey('base');
        expect($json3['difference'])->toHaveKey('subtract');
    });

    test('handles nested node structures', function (): void {
        $ownerLeaf = new Leaf(computed: new Computed(userset: 'owner'));
        $ownerNode = new Node(name: 'owner', leaf: $ownerLeaf);

        $editorLeaf = new Leaf(computed: new Computed(userset: 'editor'));
        $editorNode = new Node(name: 'editor', leaf: $editorLeaf);

        $editorWithIntersection = new Node(
            name: 'editor_node',
            leaf: $editorLeaf,
            intersection: $ownerNode,
        );

        $viewerNode = new Node(
            name: 'viewer',
            union: $editorWithIntersection,
        );

        $json = $viewerNode->jsonSerialize();
        expect($json['name'])->toBe('viewer');
        expect($json)->toHaveKey('union');
        expect($json['union']['name'])->toBe('editor_node');
    });
});
