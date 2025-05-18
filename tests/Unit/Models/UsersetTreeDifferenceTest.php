<?php

declare(strict_types=1);

use OpenFGA\Models\{Leaf, Node, UsersetTreeDifference, UsersetTreeTupleToUserset};

test('can create with base and subtract nodes', function (): void {
    $baseNode = new Node('document:1#reader');
    $subtractNode = new Node('user:1');
    $difference = new UsersetTreeDifference($baseNode, $subtractNode);

    expect($difference->getBase())->toBe($baseNode)
        ->and($difference->getSubtract())->toBe($subtractNode);
});

test('json serialize returns correct structure', function (): void {
    $baseNode = new Node(
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

    $subtractNode = new Node('user:1');
    $difference = new UsersetTreeDifference($baseNode, $subtractNode);

    $result = $difference->jsonSerialize();

    expect($result)->toMatchArray([
        'base' => $baseNode->jsonSerialize(),
        'subtract' => $subtractNode->jsonSerialize(),
    ]);
});

test('schema returns correct schema', function (): void {
    $schema = UsersetTreeDifference::schema();
    $properties = $schema->getProperties();

    expect($properties)->toHaveCount(2)
        ->and(\array_key_exists('base', $properties))->toBeTrue()
        ->and($properties['base']->name)->toBe('base')
        ->and($properties['base']->required)->toBeTrue()
        ->and(\array_key_exists('subtract', $properties))->toBeTrue()
        ->and($properties['subtract']->name)->toBe('subtract')
        ->and($properties['subtract']->required)->toBeTrue();
});
