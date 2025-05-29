<?php

declare(strict_types=1);

use OpenFGA\Models\Collections\UsersList;
use OpenFGA\Models\{Computed, Leaf, LeafInterface, UsersListUser, UsersetTreeTupleToUserset};
use OpenFGA\Schema\SchemaInterface;

describe('Leaf Model', function (): void {
    test('implements LeafInterface', function (): void {
        $users = new UsersList([
            new UsersListUser(user: 'user:alice'),
        ]);
        $leaf = new Leaf(users: $users);

        expect($leaf)->toBeInstanceOf(LeafInterface::class);
    });

    test('requires at least one parameter', function (): void {
        $this->expectException(InvalidArgumentException::class);
        new Leaf();
    });

    test('constructs with users', function (): void {
        $users = new UsersList([
            new UsersListUser(user: 'user:alice'),
            new UsersListUser(user: 'user:bob'),
        ]);

        $leaf = new Leaf(users: $users);

        expect($leaf->getUsers())->toBe($users);
        expect($leaf->getComputed())->toBeNull();
        expect($leaf->getTupleToUserset())->toBeNull();
    });

    test('constructs with computed', function (): void {
        $computed = new Computed(userset: 'viewer');

        $leaf = new Leaf(computed: $computed);

        expect($leaf->getUsers())->toBeNull();
        expect($leaf->getComputed())->toBe($computed);
        expect($leaf->getTupleToUserset())->toBeNull();
    });

    test('constructs with tuple to userset', function (): void {
        $computed = [
            new Computed(userset: 'viewer'),
        ];
        $tupleToUserset = new UsersetTreeTupleToUserset(
            tupleset: 'parent',
            computed: $computed,
        );

        $leaf = new Leaf(tupleToUserset: $tupleToUserset);

        expect($leaf->getUsers())->toBeNull();
        expect($leaf->getComputed())->toBeNull();
        expect($leaf->getTupleToUserset())->toBe($tupleToUserset);
    });

    test('serializes to JSON with only non-null fields', function (): void {
        // Leaf with users
        $users = new UsersList([
            new UsersListUser(user: 'user:alice'),
        ]);
        $leaf = new Leaf(users: $users);
        $json = $leaf->jsonSerialize();
        expect($json)->toHaveKey('users');
        expect($json['users'])->toHaveCount(1);
        expect($json['users'][0])->toBe('user:alice');

        // Leaf with computed
        $computed = new Computed(userset: 'viewer');
        $leaf = new Leaf(computed: $computed);
        expect($leaf->jsonSerialize())->toBe([
            'computed' => ['userset' => 'viewer'],
        ]);

        // Leaf with tuple to userset
        $computed = [
            new Computed(userset: 'viewer'),
        ];
        $tupleToUserset = new UsersetTreeTupleToUserset(
            tupleset: 'parent',
            computed: $computed,
        );
        $leaf = new Leaf(tupleToUserset: $tupleToUserset);
        expect($leaf->jsonSerialize())->toBe([
            'tupleToUserset' => [
                'tupleset' => 'parent',
                'computed' => [
                    ['userset' => 'viewer'],
                ],
            ],
        ]);
    });

    test('returns schema instance', function (): void {
        $schema = Leaf::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema->getClassName())->toBe(Leaf::class);

        $properties = $schema->getProperties();
        expect($properties)->toHaveCount(3);

        $propertyNames = array_keys($properties);
        expect($propertyNames)->toBe(['users', 'computed', 'tupleToUserset']);
    });

    test('schema properties have correct configuration', function (): void {
        $schema = Leaf::schema();
        $properties = $schema->getProperties();

        // Users property
        $usersProp = $properties['users'];
        expect($usersProp->name)->toBe('users');
        expect($usersProp->type)->toBe('object');
        expect($usersProp->required)->toBe(false);

        // Computed property
        $computedProp = $properties['computed'];
        expect($computedProp->name)->toBe('computed');
        expect($computedProp->type)->toBe('object');
        expect($computedProp->required)->toBe(false);

        // TupleToUserset property
        $tupleToUsersetProp = $properties['tupleToUserset'];
        expect($tupleToUsersetProp->name)->toBe('tupleToUserset');
        expect($tupleToUsersetProp->type)->toBe('object');
        expect($tupleToUsersetProp->required)->toBe(false);
    });

    test('schema is cached', function (): void {
        $schema1 = Leaf::schema();
        $schema2 = Leaf::schema();

        expect($schema1)->toBe($schema2);
    });

    test('only one field should be set at a time', function (): void {
        $users = new UsersList([
            new UsersListUser(user: 'user:alice'),
        ]);
        $computedItem = new Computed(userset: 'viewer');
        $computedArray = [
            new Computed(userset: 'viewer'),
        ];
        $tupleToUserset = new UsersetTreeTupleToUserset(
            tupleset: 'parent',
            computed: $computedArray,
        );

        $leaf1 = new Leaf(users: $users);
        $leaf2 = new Leaf(computed: $computedItem);
        $leaf3 = new Leaf(tupleToUserset: $tupleToUserset);

        expect($leaf1->jsonSerialize())->toHaveCount(1);
        expect($leaf2->jsonSerialize())->toHaveCount(1);
        expect($leaf3->jsonSerialize())->toHaveCount(1);
    });

    test('handles typical authorization patterns', function (): void {
        // Pattern 1: Direct users assignment
        $users = new UsersList([
            new UsersListUser(user: 'user:alice'),
            new UsersListUser(user: 'user:bob'),
            new UsersListUser(user: 'group:engineering#member'),
        ]);
        $directLeaf = new Leaf(users: $users);

        $json = $directLeaf->jsonSerialize();
        expect($json)->toHaveKey('users');
        expect($json['users'])->toHaveCount(3);
        expect($json['users'])->toBe([
            'user:alice',
            'user:bob',
            'group:engineering#member',
        ]);

        // Pattern 2: Computed from relation
        $computedLeaf = new Leaf(
            computed: new Computed(userset: 'owner'),
        );

        expect($computedLeaf->jsonSerialize())->toBe([
            'computed' => ['userset' => 'owner'],
        ]);

        // Pattern 3: Tuple to userset for hierarchical permissions
        $computed = [
            new Computed(userset: 'viewer'),
        ];
        $hierarchicalLeaf = new Leaf(
            tupleToUserset: new UsersetTreeTupleToUserset(
                tupleset: 'parent',
                computed: $computed,
            ),
        );

        expect($hierarchicalLeaf->jsonSerialize())->toBe([
            'tupleToUserset' => [
                'tupleset' => 'parent',
                'computed' => [
                    ['userset' => 'viewer'],
                ],
            ],
        ]);
    });
});
