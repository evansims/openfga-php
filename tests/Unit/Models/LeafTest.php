<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models;

use InvalidArgumentException;
use OpenFGA\Models\{Computed, Leaf, Node, UsersList, UsersetTreeTupleToUserset};

beforeEach(function (): void {
    $this->users = new UsersList();
    $this->computed = new Computed('document:1#reader');
    $this->base = new Node('document:1#reader');
    $this->subtract = new Node('user:1');
    $this->tupleToUserset = new UsersetTreeTupleToUserset($this->base, $this->subtract);
});

test('constructor with users', function (): void {
    $leaf = new Leaf(users: $this->users);

    expect($leaf->getUsers())->toBe($this->users)
        ->and($leaf->getComputed())->toBeNull()
        ->and($leaf->getTupleToUserset())->toBeNull();
});

test('constructor with computed', function (): void {
    $leaf = new Leaf(computed: $this->computed);

    expect($leaf->getComputed())->toBe($this->computed)
        ->and($leaf->getUsers())->toBeNull()
        ->and($leaf->getTupleToUserset())->toBeNull();
});

test('constructor with tupleToUserset', function (): void {
    $leaf = new Leaf(tupleToUserset: $this->tupleToUserset);

    expect($leaf->getTupleToUserset())->toBe($this->tupleToUserset)
        ->and($leaf->getUsers())->toBeNull()
        ->and($leaf->getComputed())->toBeNull();
});

test('constructor throws exception when no properties provided', function (): void {
    $this->expectException(InvalidArgumentException::class);
    new Leaf();
});

test('json serialize with users', function (): void {
    $leaf = new Leaf(users: $this->users);

    $result = $leaf->jsonSerialize();

    expect($result)->toBe([
        'users' => $this->users->jsonSerialize(),
    ]);
});

test('json serialize with computed', function (): void {
    $leaf = new Leaf(computed: $this->computed);

    $result = $leaf->jsonSerialize();

    expect($result)->toBe([
        'computed' => $this->computed->jsonSerialize(),
    ]);
});

test('json serialize with tupleToUserset', function (): void {
    $leaf = new Leaf(tupleToUserset: $this->tupleToUserset);

    $result = $leaf->jsonSerialize();

    expect($result)->toBe([
        'tupleToUserset' => $this->tupleToUserset->jsonSerialize(),
    ]);
});

test('schema', function (): void {
    $schema = Leaf::schema();

    expect($schema->getClassName())->toBe(Leaf::class);

    $properties = $schema->getProperties();
    $this->assertIsArray($properties);
    $this->assertCount(3, $properties);

    // Check users property
    $this->assertArrayHasKey('users', $properties);
    $this->assertSame('users', $properties['users']->name);
    $this->assertSame('OpenFGA\\Models\\UsersList', $properties['users']->type);
    $this->assertFalse($properties['users']->required);

    // Check computed property
    $this->assertArrayHasKey('computed', $properties);
    $this->assertSame('computed', $properties['computed']->name);
    $this->assertSame('OpenFGA\\Models\\Computed', $properties['computed']->type);
    $this->assertFalse($properties['computed']->required);

    // Check tupleToUserset property
    $this->assertArrayHasKey('tupleToUserset', $properties);
    $this->assertSame('tupleToUserset', $properties['tupleToUserset']->name);
    $this->assertSame('OpenFGA\\Models\\UsersetTreeTupleToUserset', $properties['tupleToUserset']->type);
    $this->assertFalse($properties['tupleToUserset']->required);
});
