<?php

declare(strict_types=1);

use OpenFGA\Models\{Assertion, AssertionTupleKey, TupleKey};
use OpenFGA\Models\Collections\{Assertions, AssertionsInterface, TupleKeys};

describe('Assertions Collection', function (): void {
    test('implements AssertionsInterface', function (): void {
        $collection = new Assertions();

        expect($collection)->toBeInstanceOf(AssertionsInterface::class);
    });

    test('constructs empty collection', function (): void {
        $collection = new Assertions();

        expect($collection->count())->toBe(0);
        expect($collection->isEmpty())->toBe(true);
        expect($collection->toArray())->toBe([]);
    });

    test('constructs with single Assertion', function (): void {
        $tupleKey = new AssertionTupleKey(
            user: 'user:anne',
            relation: 'viewer',
            object: 'document:roadmap',
        );
        $assertion = new Assertion(tupleKey: $tupleKey, expectation: true);

        $collection = new Assertions($assertion);

        expect($collection->count())->toBe(1);
        expect($collection->isEmpty())->toBe(false);
        // Collection method removed - not available
    });

    test('constructs with multiple Assertions', function (): void {
        $assertion1 = new Assertion(
            tupleKey: new AssertionTupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1'),
            expectation: true,
        );
        $assertion2 = new Assertion(
            tupleKey: new AssertionTupleKey(user: 'user:bob', relation: 'editor', object: 'document:2'),
            expectation: false,
        );
        $assertion3 = new Assertion(
            tupleKey: new AssertionTupleKey(user: 'user:charlie', relation: 'owner', object: 'document:3'),
            expectation: true,
        );

        $collection = new Assertions($assertion1, $assertion2, $assertion3);

        expect($collection->count())->toBe(3);
        expect($collection->toArray())->toBe([$assertion1, $assertion2, $assertion3]);
    });

    test('constructs with array of Assertions', function (): void {
        $assertions = [
            new Assertion(
                tupleKey: new AssertionTupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1'),
                expectation: true,
            ),
            new Assertion(
                tupleKey: new AssertionTupleKey(user: 'user:bob', relation: 'editor', object: 'document:2'),
                expectation: false,
            ),
        ];

        $collection = new Assertions($assertions);

        expect($collection->count())->toBe(2);
        expect($collection->toArray())->toBe($assertions);
    });

    test('adds Assertion to collection', function (): void {
        $collection = new Assertions();
        $assertion = new Assertion(
            tupleKey: new AssertionTupleKey(user: 'user:anne', relation: 'viewer', object: 'document:roadmap'),
            expectation: true,
        );

        $result = $collection->add($assertion);

        expect($result)->toBe($collection); // Fluent interface
        expect($collection->count())->toBe(1);
        // Collection method removed - not available
    });

    test('handles assertions with contextual tuples', function (): void {
        $contextualTuple = new TupleKey(
            user: 'group:admins#member',
            relation: 'editor',
            object: 'document:roadmap',
        );
        $contextualTuples = new TupleKeys([$contextualTuple]);

        $assertion = new Assertion(
            tupleKey: new AssertionTupleKey(user: 'user:anne', relation: 'viewer', object: 'document:roadmap'),
            expectation: true,
            contextualTuples: $contextualTuples,
        );

        $collection = new Assertions($assertion);

        expect($collection->count())->toBe(1);
        expect($collection->first()->getContextualTuples())->toBe($contextualTuples);
    });

    test('handles assertions with context', function (): void {
        $context = ['region' => 'us-east', 'department' => 'engineering'];

        $assertion = new Assertion(
            tupleKey: new AssertionTupleKey(user: 'user:anne', relation: 'viewer', object: 'document:roadmap'),
            expectation: true,
            context: $context,
        );

        $collection = new Assertions($assertion);

        expect($collection->count())->toBe(1);
        expect($collection->first()->getContext())->toBe($context);
    });

    test('mixes positive and negative expectations', function (): void {
        $assertions = [
            new Assertion(
                tupleKey: new AssertionTupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1'),
                expectation: true,
            ),
            new Assertion(
                tupleKey: new AssertionTupleKey(user: 'user:bob', relation: 'editor', object: 'document:1'),
                expectation: false,
            ),
            new Assertion(
                tupleKey: new AssertionTupleKey(user: 'user:charlie', relation: 'owner', object: 'document:1'),
                expectation: true,
            ),
        ];

        $collection = new Assertions(...$assertions);

        $expectations = array_map(fn ($a) => $a->getExpectation(), $collection->toArray());
        expect($expectations)->toBe([true, false, true]);
    });

    test('clear removes all items', function (): void {
        $collection = new Assertions(
            new Assertion(
                tupleKey: new AssertionTupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1'),
                expectation: true,
            ),
            new Assertion(
                tupleKey: new AssertionTupleKey(user: 'user:bob', relation: 'editor', object: 'document:2'),
                expectation: false,
            ),
        );

        expect($collection->count())->toBe(2);

        $collection->clear();

        expect($collection->count())->toBe(0);
        expect($collection->isEmpty())->toBe(true);
    });

    test('iterates over collection', function (): void {
        $assertions = [
            new Assertion(
                tupleKey: new AssertionTupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1'),
                expectation: true,
            ),
            new Assertion(
                tupleKey: new AssertionTupleKey(user: 'user:bob', relation: 'editor', object: 'document:2'),
                expectation: false,
            ),
        ];

        $collection = new Assertions(...$assertions);

        $iterated = [];
        foreach ($collection as $index => $assertion) {
            $iterated[$index] = $assertion;
        }

        expect($iterated)->toBe($assertions);
    });

    test('serializes to JSON', function (): void {
        $assertion1 = new Assertion(
            tupleKey: new AssertionTupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1'),
            expectation: true,
        );
        $assertion2 = new Assertion(
            tupleKey: new AssertionTupleKey(user: 'user:bob', relation: 'editor', object: 'document:2'),
            expectation: false,
        );

        $collection = new Assertions($assertion1, $assertion2);

        $json = $collection->jsonSerialize();

        expect($json)->toBe([
            $assertion1->jsonSerialize(),
            $assertion2->jsonSerialize(),
        ]);
    });

    test('gets item by index', function (): void {
        $assertion1 = new Assertion(
            tupleKey: new AssertionTupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1'),
            expectation: true,
        );
        $assertion2 = new Assertion(
            tupleKey: new AssertionTupleKey(user: 'user:bob', relation: 'editor', object: 'document:2'),
            expectation: false,
        );

        $collection = new Assertions($assertion1, $assertion2);

        expect($collection->get(0))->toBe($assertion1);
        expect($collection->get(1))->toBe($assertion2);
        expect($collection->get(2))->toBeNull();
    });

    test('throws TypeError when adding wrong type', function (): void {
        $collection = new Assertions();
        $wrongType = new stdClass();

        expect(fn () => $collection->add($wrongType))->toThrow(TypeError::class);
    });

    test('maintains insertion order', function (): void {
        $assertions = [];
        for ($i = 0; $i < 5; ++$i) {
            $assertions[] = new Assertion(
                tupleKey: new AssertionTupleKey(
                    user: "user:user{$i}",
                    relation: 'viewer',
                    object: "document:doc{$i}",
                ),
                expectation: 0 === $i % 2, // Alternate true/false
            );
        }

        $collection = new Assertions(...$assertions);

        expect($collection->toArray())->toBe($assertions);

        // Verify expectations alternate
        $expectations = array_map(fn ($a) => $a->getExpectation(), $collection->toArray());
        expect($expectations)->toBe([true, false, true, false, true]);
    });

    test('handles complex assertion scenarios', function (): void {
        // Create contextual tuples
        $contextualTuples = new TupleKeys([
            new TupleKey(user: 'group:admins#member', relation: 'admin', object: 'org:acme'),
            new TupleKey(user: 'user:anne', relation: 'member', object: 'group:admins'),
        ]);

        // Create context
        $context = ['ip_address' => '192.168.1.1', 'time_of_day' => 'business_hours'];

        // Create assertion with all optional fields
        $complexAssertion = new Assertion(
            tupleKey: new AssertionTupleKey(user: 'user:anne', relation: 'admin', object: 'org:acme'),
            expectation: true,
            contextualTuples: $contextualTuples,
            context: $context,
        );

        $collection = new Assertions($complexAssertion);

        expect($collection->count())->toBe(1);

        $retrieved = $collection->first();
        expect($retrieved->getExpectation())->toBe(true);
        expect($retrieved->getContextualTuples())->toBe($contextualTuples);
        expect($retrieved->getContext())->toBe($context);
    });
});
