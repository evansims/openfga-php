<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models;

use OpenFGA\Models\{Leaf, Node, UsersList, UsersetTreeDifference};

beforeEach(function (): void {
    $this->name = 'test-node';
    $this->leaf = new Leaf(new UsersList());
    $this->difference = new UsersetTreeDifference(new Node('base'), new Node('subtract'));
    $this->union = new Node('union');
    $this->intersection = new Node('intersection');
});

test('constructor with name only', function (): void {
    $node = new Node($this->name);

    expect($node->getName())->toBe($this->name)
        ->and($node->getLeaf())->toBeNull()
        ->and($node->getDifference())->toBeNull()
        ->and($node->getUnion())->toBeNull()
        ->and($node->getIntersection())->toBeNull();
});

test('constructor with all properties', function (): void {
    $node = new Node(
        name: $this->name,
        leaf: $this->leaf,
        difference: $this->difference,
        union: $this->union,
        intersection: $this->intersection,
    );

    expect($node->getName())->toBe($this->name)
        ->and($node->getLeaf())->toBe($this->leaf)
        ->and($node->getDifference())->toBe($this->difference)
        ->and($node->getUnion())->toBe($this->union)
        ->and($node->getIntersection())->toBe($this->intersection);
});

test('json serialize with all properties', function (): void {
    $node = new Node(
        name: $this->name,
        leaf: $this->leaf,
        difference: $this->difference,
        union: $this->union,
        intersection: $this->intersection,
    );

    $result = $node->jsonSerialize();

    expect($result)->toBe([
        'name' => $this->name,
        'leaf' => $this->leaf->jsonSerialize(),
        'difference' => $this->difference->jsonSerialize(),
        'union' => $this->union->jsonSerialize(),
        'intersection' => $this->intersection->jsonSerialize(),
    ]);
});

test('json serialize with name only', function (): void {
    $node = new Node($this->name);

    $result = $node->jsonSerialize();

    expect($result)->toBe([
        'name' => $this->name,
    ]);
});

test('schema', function (): void {
    $schema = Node::schema();

    expect($schema->getClassName())->toBe(Node::class);

    $properties = $schema->getProperties();
    $this->assertIsArray($properties);
    $this->assertCount(5, $properties);

    // Check name property
    $this->assertArrayHasKey('name', $properties);
    $this->assertSame('name', $properties['name']->name);
    $this->assertSame('string', $properties['name']->type);
    $this->assertTrue($properties['name']->required);

    // Check leaf property
    $this->assertArrayHasKey('leaf', $properties);
    $this->assertSame('leaf', $properties['leaf']->name);
    $this->assertSame('OpenFGA\\Models\\Leaf', $properties['leaf']->type);
    $this->assertFalse($properties['leaf']->required);

    // Check difference property
    $this->assertArrayHasKey('difference', $properties);
    $this->assertSame('difference', $properties['difference']->name);
    $this->assertSame('OpenFGA\\Models\\UsersetTreeDifference', $properties['difference']->type);
    $this->assertFalse($properties['difference']->required);

    // Check union property
    $this->assertArrayHasKey('union', $properties);
    $this->assertSame('union', $properties['union']->name);
    $this->assertSame('self', $properties['union']->type);
    $this->assertFalse($properties['union']->required);

    // Check intersection property
    $this->assertArrayHasKey('intersection', $properties);
    $this->assertSame('intersection', $properties['intersection']->name);
    $this->assertSame('self', $properties['intersection']->type);
    $this->assertFalse($properties['intersection']->required);
});
