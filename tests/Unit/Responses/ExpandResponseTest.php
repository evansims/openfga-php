<?php

declare(strict_types=1);

use OpenFGA\Models\{Node, UsersetTree};
use OpenFGA\Responses\{ExpandResponse, ExpandResponseInterface};

test('ExpandResponse implements ExpandResponseInterface', function (): void {
    $response = new ExpandResponse();
    expect($response)->toBeInstanceOf(ExpandResponseInterface::class);
});

test('ExpandResponse constructs with null tree', function (): void {
    $response = new ExpandResponse();
    expect($response->getTree())->toBeNull();
});

test('ExpandResponse constructs with UsersetTree', function (): void {
    $root = new Node(name: 'viewer');
    $tree = new UsersetTree($root);

    $response = new ExpandResponse($tree);
    expect($response->getTree())->toBe($tree);
});

// Note: fromResponse method testing would require integration tests due to SchemaValidator being final
// These tests focus on the model's direct functionality

test('ExpandResponse schema returns expected structure', function (): void {
    $schema = ExpandResponse::schema();

    expect($schema)->toBeInstanceOf(OpenFGA\Schema\SchemaInterface::class);
    expect($schema->getClassName())->toBe(ExpandResponse::class);

    $properties = $schema->getProperties();
    expect($properties)->toHaveCount(1);
    expect($properties['tree']->name)->toBe('tree');
    expect($properties['tree']->type)->toBe(UsersetTree::class);
    expect($properties['tree']->required)->toBeFalse();
});

test('ExpandResponse schema is cached', function (): void {
    $schema1 = ExpandResponse::schema();
    $schema2 = ExpandResponse::schema();

    expect($schema1)->toBe($schema2);
});

test('ExpandResponse handles complex tree structure', function (): void {
    $root = new Node(name: 'document:budget#viewer');
    $tree = new UsersetTree($root);

    $response = new ExpandResponse($tree);

    expect($response->getTree())->toBe($tree);
    expect($response->getTree()->getRoot())->toBe($root);
});
