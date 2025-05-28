<?php

declare(strict_types=1);

use OpenFGA\Models\{Assertion, AssertionInterface, AssertionTupleKey, TupleKey};
use OpenFGA\Models\Collections\TupleKeys;
use OpenFGA\Schema\SchemaInterface;

describe('Assertion Model', function (): void {
    test('implements AssertionInterface', function (): void {
        $tupleKey = new AssertionTupleKey(
            user: 'user:anne',
            relation: 'viewer',
            object: 'document:roadmap',
        );

        $assertion = new Assertion(
            tupleKey: $tupleKey,
            expectation: true,
        );

        expect($assertion)->toBeInstanceOf(AssertionInterface::class);
    });

    test('constructs with required parameters only', function (): void {
        $tupleKey = new AssertionTupleKey(
            user: 'user:anne',
            relation: 'viewer',
            object: 'document:roadmap',
        );

        $assertion = new Assertion(
            tupleKey: $tupleKey,
            expectation: true,
        );

        expect($assertion->getTupleKey())->toBe($tupleKey);
        expect($assertion->getExpectation())->toBe(true);
        expect($assertion->getContextualTuples())->toBeNull();
        expect($assertion->getContext())->toBeNull();
    });

    test('constructs with all parameters', function (): void {
        $tupleKey = new AssertionTupleKey(
            user: 'user:anne',
            relation: 'viewer',
            object: 'document:roadmap',
        );

        $contextualTuple = new TupleKey(
            user: 'user:bob',
            relation: 'editor',
            object: 'document:roadmap',
        );

        $contextualTuples = new TupleKeys([$contextualTuple]);
        $context = ['key' => 'value', 'foo' => 'bar'];

        $assertion = new Assertion(
            tupleKey: $tupleKey,
            expectation: false,
            contextualTuples: $contextualTuples,
            context: $context,
        );

        expect($assertion->getTupleKey())->toBe($tupleKey);
        expect($assertion->getExpectation())->toBe(false);
        expect($assertion->getContextualTuples())->toBe($contextualTuples);
        expect($assertion->getContext())->toBe($context);
    });

    test('serializes to JSON correctly with required fields only', function (): void {
        $tupleKey = new AssertionTupleKey(
            user: 'user:anne',
            relation: 'viewer',
            object: 'document:roadmap',
        );

        $assertion = new Assertion(
            tupleKey: $tupleKey,
            expectation: true,
        );

        $json = $assertion->jsonSerialize();

        expect($json)->toHaveKeys(['tuple_key', 'expectation']);
        expect($json)->not->toHaveKeys(['contextual_tuples', 'context']);
        expect($json['tuple_key'])->toBe($tupleKey->jsonSerialize());
        expect($json['expectation'])->toBe(true);
    });

    test('serializes to JSON correctly with all fields', function (): void {
        $tupleKey = new AssertionTupleKey(
            user: 'user:anne',
            relation: 'viewer',
            object: 'document:roadmap',
        );

        $contextualTuple = new TupleKey(
            user: 'user:bob',
            relation: 'editor',
            object: 'document:roadmap',
        );

        $contextualTuples = new TupleKeys([$contextualTuple]);
        $context = ['key' => 'value'];

        $assertion = new Assertion(
            tupleKey: $tupleKey,
            expectation: false,
            contextualTuples: $contextualTuples,
            context: $context,
        );

        $json = $assertion->jsonSerialize();

        expect($json)->toHaveKeys(['tuple_key', 'expectation', 'contextual_tuples', 'context']);
        expect($json['tuple_key'])->toBe($tupleKey->jsonSerialize());
        expect($json['expectation'])->toBe(false);
        expect($json['contextual_tuples'])->toBe($contextualTuples->jsonSerialize()['tuple_keys']);
        expect($json['context'])->toBe($context);
    });

    test('handles empty context array', function (): void {
        $tupleKey = new AssertionTupleKey(
            user: 'user:anne',
            relation: 'viewer',
            object: 'document:roadmap',
        );

        $assertion = new Assertion(
            tupleKey: $tupleKey,
            expectation: true,
            context: [],
        );

        expect($assertion->getContext())->toBe([]);
        expect($assertion->jsonSerialize())->toHaveKey('context');
    });

    test('returns schema instance', function (): void {
        $schema = Assertion::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema->getClassName())->toBe(Assertion::class);

        $properties = $schema->getProperties();
        expect($properties)->toHaveCount(4);

        $propertyNames = array_keys($properties);
        expect($propertyNames)->toBe(['tuple_key', 'expectation', 'contextual_tuples', 'context']);
    });

    test('schema is cached', function (): void {
        $schema1 = Assertion::schema();
        $schema2 = Assertion::schema();

        expect($schema1)->toBe($schema2);
    });

    test('expectation can be false', function (): void {
        $tupleKey = new AssertionTupleKey(
            user: 'user:anne',
            relation: 'viewer',
            object: 'document:roadmap',
        );

        $assertion = new Assertion(
            tupleKey: $tupleKey,
            expectation: false,
        );

        expect($assertion->getExpectation())->toBe(false);
        expect($assertion->jsonSerialize()['expectation'])->toBe(false);
    });

    test('jsonSerialize handles null contextualTuples', function (): void {
        $tupleKey = new AssertionTupleKey(
            user: 'user:anne',
            relation: 'viewer',
            object: 'document:roadmap',
        );

        $assertion = new Assertion(
            tupleKey: $tupleKey,
            expectation: true,
            contextualTuples: null,
        );

        $json = $assertion->jsonSerialize();

        expect($json)->not->toHaveKey('contextual_tuples');
        expect($json)->toHaveKeys(['tuple_key', 'expectation']);
    });

    test('jsonSerialize extracts tuple_keys when present in contextualTuples', function (): void {
        $tupleKey = new AssertionTupleKey(
            user: 'user:anne',
            relation: 'viewer',
            object: 'document:roadmap',
        );

        $contextualTuple = new TupleKey(
            user: 'user:bob',
            relation: 'editor',
            object: 'document:roadmap',
        );

        // TupleKeys wraps its content with 'tuple_keys'
        $contextualTuples = new TupleKeys([$contextualTuple]);

        $assertion = new Assertion(
            tupleKey: $tupleKey,
            expectation: true,
            contextualTuples: $contextualTuples,
        );

        $json = $assertion->jsonSerialize();
        $contextualTuplesJson = $contextualTuples->jsonSerialize();

        expect($json)->toHaveKey('contextual_tuples');
        expect($contextualTuplesJson)->toHaveKey('tuple_keys');

        // Verify that the logic extracts the 'tuple_keys' value specifically
        expect($json['contextual_tuples'])->toBe($contextualTuplesJson['tuple_keys']);
        expect($json['contextual_tuples'])->not->toBe($contextualTuplesJson);
    });

    test('jsonSerialize conditional logic covers both branches of tuple_keys extraction', function (): void {
        $tupleKey = new AssertionTupleKey(
            user: 'user:anne',
            relation: 'viewer',
            object: 'document:roadmap',
        );

        $contextualTuple = new TupleKey(
            user: 'user:bob',
            relation: 'editor',
            object: 'document:roadmap',
        );

        $contextualTuples = new TupleKeys([$contextualTuple]);

        $assertion = new Assertion(
            tupleKey: $tupleKey,
            expectation: true,
            contextualTuples: $contextualTuples,
        );

        $json = $assertion->jsonSerialize();

        // Test that the conditional handles the tuple_keys key properly
        expect($json)->toHaveKey('contextual_tuples');
        expect($json['contextual_tuples'])->toBeArray();

        // The contextual_tuples should be the unwrapped content, not the wrapped version
        $wrappedVersion = $contextualTuples->jsonSerialize();
        expect($wrappedVersion)->toHaveKey('tuple_keys');
        expect($json['contextual_tuples'])->toBe($wrappedVersion['tuple_keys']);

        // Verify structure: the contextual_tuples in assertion should be an array of tuple objects
        expect($json['contextual_tuples'])->toBeArray();
        expect($json['contextual_tuples'])->toHaveCount(1);
        expect($json['contextual_tuples'][0])->toHaveKeys(['user', 'relation', 'object']);
    });
});
