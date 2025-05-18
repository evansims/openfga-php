<?php

declare(strict_types=1);

use OpenFGA\Models\{Leaf, Node, UsersetTree, UsersetTreeTupleToUserset};

test('can create with root node', function (): void {
    $node = new Node('document:1#reader');
    $usersetTree = new UsersetTree($node);

    expect($usersetTree->getRoot())->toBe($node);
});

test('json serialize returns correct structure', function (): void {
    $node = new Node(
        name: 'document:1#reader',
        leaf: new Leaf(
            users: null,
            computed: null,
            tupleToUserset: new UsersetTreeTupleToUserset(
                new Node('document:1#reader'),
                new Node('user:1'),
            ),
        ),
    );

    $usersetTree = new UsersetTree($node);
    $result = $usersetTree->jsonSerialize();

    expect($result)->toMatchArray([
        'root' => $node->jsonSerialize(),
    ]);
});

test('schema returns correct schema', function (): void {
    $schema = UsersetTree::schema();
    $properties = $schema->getProperties();

    expect($properties)->toHaveCount(1)
        ->and(\array_key_exists('root', $properties))->toBeTrue()
        ->and($properties['root']->name)->toBe('root')
        ->and($properties['root']->required)->toBeTrue();
});
