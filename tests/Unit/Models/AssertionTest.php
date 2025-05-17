<?php

declare(strict_types=1);

use OpenFGA\Models\{Assertion, AssertionTupleKey, TupleKey, TupleKeys};

test('constructor and getters', function (): void {
    $tupleKey = new AssertionTupleKey('document:1', 'reader', 'user:1');
    $contextualTuples = new TupleKeys([
        new TupleKey('document:1', 'writer', 'user:2'),
    ]);
    $context = ['key' => 'value'];
    $expectation = true;

    $assertion = new Assertion(
        tupleKey: $tupleKey,
        expectation: $expectation,
        contextualTuples: $contextualTuples,
        context: $context,
    );

    expect($assertion->getTupleKey())->toBe($tupleKey)
        ->and($assertion->getExpectation())->toBe($expectation)
        ->and($assertion->getContextualTuples())->toBe($contextualTuples)
        ->and($assertion->getContext())->toBe($context);
});

test('json serialize with all properties', function (): void {
    $tupleKey = new AssertionTupleKey('document:1', 'reader', 'user:1');
    $contextualTuples = new TupleKeys([
        new TupleKey('document:1', 'writer', 'user:2'),
    ]);
    $context = ['key' => 'value'];
    $expectation = true;

    $assertion = new Assertion(
        tupleKey: $tupleKey,
        expectation: $expectation,
        contextualTuples: $contextualTuples,
        context: $context,
    );

    $result = $assertion->jsonSerialize();

    expect($result)->toMatchArray([
        'tuple_key' => $tupleKey->jsonSerialize(),
        'expectation' => $expectation,
        'contextual_tuples' => $contextualTuples->jsonSerialize(),
        'context' => $context,
    ]);
});

test('json serialize with null values', function (): void {
    $tupleKey = new AssertionTupleKey('document:1', 'reader', 'user:1');
    $expectation = true;

    $assertion = new Assertion(
        tupleKey: $tupleKey,
        expectation: $expectation,
        contextualTuples: null,
        context: null,
    );

    $result = $assertion->jsonSerialize();

    expect($result)->toMatchArray([
        'tuple_key' => $tupleKey->jsonSerialize(),
        'expectation' => $expectation,
    ]);

    expect($result)->not->toHaveKey('contextual_tuples')
        ->not->toHaveKey('context');
});
