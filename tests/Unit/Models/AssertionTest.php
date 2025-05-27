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
        expect($json['contextual_tuples'])->toBe($contextualTuples->jsonSerialize());
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
});
