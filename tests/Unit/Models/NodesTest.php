<?php

declare(strict_types=1);

use OpenFGA\Models\{Node, Nodes};

use function strtoupper;

test('empty collection', function (): void {
    $nodes = new Nodes();

    expect($nodes)->toHaveCount(0);
});
test('add node', function (): void {
    $nodes = new Nodes();
    $node = new Node('node1');

    $nodes[] = $node;

    expect($nodes)->toHaveCount(1)
        ->and($nodes[0])->toBe($node);
});
test('create with nodes', function (): void {
    $node1 = new Node('node1');
    $node2 = new Node('node2');

    $nodes = new Nodes([$node1, $node2]);

    expect($nodes)->toHaveCount(2)
        ->and($nodes[0])->toBe($node1)
        ->and($nodes[1])->toBe($node2);
});
test('json serialize', function (): void {
    $node1 = new Node('node1');
    $node2 = new Node('node2');
    $nodes = new Nodes([$node1, $node2]);

    $result = $nodes->jsonSerialize();

    expect($result)->toBe([
        $node1->jsonSerialize(),
        $node2->jsonSerialize(),
    ]);
});
test('filter nodes', function (): void {
    $node1 = new Node('node1');
    $node2 = new Node('node2');
    $nodes = new Nodes([$node1, $node2]);

    $filtered = $nodes->filter(fn (Node $n) => 'node1' === $n->getName());

    expect($filtered)->toHaveCount(1)
        ->and($filtered[0])->toBe($node1);
});
test('find node by name', function (): void {
    $node1 = new Node('node1');
    $node2 = new Node('node2');
    $nodes = new Nodes([$node1, $node2]);

    $found = $nodes->first(fn (Node $n) => 'node2' === $n->getName());

    expect($found)->toBe($node2);
});
test('map nodes to uppercase names', function (): void {
    $node1 = new Node('node1');
    $node2 = new Node('node2');
    $nodes = new Nodes([$node1, $node2]);

    $mapped = $nodes->map(Node::class, fn (Node $n) => new Node(strtoupper($n->getName())));

    expect($mapped)
        ->toBeInstanceOf(Nodes::class)
        ->toHaveCount(2)
        ->and($mapped[0]->getName())->toBe('NODE1')
        ->and($mapped[1]->getName())->toBe('NODE2');
});
test('some nodes match name', function (): void {
    $nodes = new Nodes([new Node('node1'), new Node('node2')]);

    $hasNode1 = $nodes->some(fn (Node $n) => 'node1' === $n->getName());
    $hasNode3 = $nodes->some(fn (Node $n) => 'node3' === $n->getName());

    expect($hasNode1)->toBeTrue()
        ->and($hasNode3)->toBeFalse();
});
test('convert to array', function (): void {
    $node1 = new Node('node1');
    $node2 = new Node('node2');
    $nodes = new Nodes([$node1, $node2]);

    $array = $nodes->toArray();

    expect($array)->toBe([$node1, $node2]);
});
test('can check if offset exists', function (): void {
    $node = new Node('node1');
    $nodes = new Nodes($node);

    expect(isset($nodes[0]))->toBeTrue()
        ->and(isset($nodes[1]))->toBeFalse();
});
test('can unset offset', function (): void {
    $node1 = new Node('node1');
    $node2 = new Node('node2');
    $nodes = new Nodes([$node1, $node2]);

    unset($nodes[0]);

    expect($nodes)->toHaveCount(1)
        ->and($nodes[0])->toBe($node2);
});
test('schema returns correct schema', function (): void {
    $schema = Nodes::schema();

    expect($schema->getItemType())->toBe(Node::class);
});
