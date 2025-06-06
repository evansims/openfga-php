<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models;

use OpenFGA\Models\{Assertion, AssertionInterface, AssertionTupleKey, TupleKey};
use OpenFGA\Models\Collections\TupleKeys;
use OpenFGA\Schemas\SchemaInterface;

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
        $serialized = $contextualTuples->jsonSerialize();
        expect($json['contextual_tuples'])->toBe($serialized['tuple_keys']);
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

    test('creates from array with required fields only', function (): void {
        $data = [
            'tuple_key' => [
                'user' => 'user:anne',
                'relation' => 'viewer',
                'object' => 'document:roadmap',
            ],
            'expectation' => true,
        ];

        $assertion = Assertion::fromArray($data);

        expect($assertion)->toBeInstanceOf(Assertion::class);
        expect($assertion->getTupleKey())->toBeInstanceOf(AssertionTupleKey::class);
        expect($assertion->getTupleKey()->getUser())->toBe('user:anne');
        expect($assertion->getTupleKey()->getRelation())->toBe('viewer');
        expect($assertion->getTupleKey()->getObject())->toBe('document:roadmap');
        expect($assertion->getExpectation())->toBe(true);
        expect($assertion->getContextualTuples())->toBeNull();
        expect($assertion->getContext())->toBeNull();
    });

    test('creates from array with contextual tuples as direct array', function (): void {
        $data = [
            'tuple_key' => [
                'user' => 'user:anne',
                'relation' => 'viewer',
                'object' => 'document:roadmap',
            ],
            'expectation' => false,
            'contextual_tuples' => [
                [
                    'user' => 'group:admins#member',
                    'relation' => 'editor',
                    'object' => 'document:roadmap',
                ],
                [
                    'user' => 'user:bob',
                    'relation' => 'owner',
                    'object' => 'folder:root',
                ],
            ],
        ];

        $assertion = Assertion::fromArray($data);

        expect($assertion->getExpectation())->toBe(false);
        expect($assertion->getContextualTuples())->toBeInstanceOf(TupleKeys::class);
        expect($assertion->getContextualTuples()->count())->toBe(2);

        $tuples = $assertion->getContextualTuples()->toArray();
        expect($tuples[0]->getUser())->toBe('group:admins#member');
        expect($tuples[1]->getUser())->toBe('user:bob');
    });

    test('creates from array with contextual tuples wrapped in tuple_keys', function (): void {
        $data = [
            'tuple_key' => [
                'user' => 'user:anne',
                'relation' => 'viewer',
                'object' => 'document:roadmap',
            ],
            'expectation' => true,
            'contextual_tuples' => [
                'tuple_keys' => [
                    [
                        'user' => 'user:bob',
                        'relation' => 'editor',
                        'object' => 'document:roadmap',
                    ],
                ],
            ],
        ];

        $assertion = Assertion::fromArray($data);

        expect($assertion->getContextualTuples())->toBeInstanceOf(TupleKeys::class);
        expect($assertion->getContextualTuples()->count())->toBe(1);
        expect($assertion->getContextualTuples()->first()->getUser())->toBe('user:bob');
    });

    test('creates from array with contextual tuples containing TupleKeyInterface objects', function (): void {
        $existingTuple = new TupleKey(
            user: 'user:existing',
            relation: 'viewer',
            object: 'document:test',
        );

        $data = [
            'tuple_key' => [
                'user' => 'user:anne',
                'relation' => 'viewer',
                'object' => 'document:roadmap',
            ],
            'expectation' => true,
            'contextual_tuples' => [
                $existingTuple,
                [
                    'user' => 'user:new',
                    'relation' => 'editor',
                    'object' => 'document:new',
                ],
            ],
        ];

        $assertion = Assertion::fromArray($data);

        expect($assertion->getContextualTuples()->count())->toBe(2);
        expect($assertion->getContextualTuples()->toArray()[0])->toBe($existingTuple);
        expect($assertion->getContextualTuples()->toArray()[1]->getUser())->toBe('user:new');
    });

    test('skips contextual tuples with conditions', function (): void {
        $data = [
            'tuple_key' => [
                'user' => 'user:anne',
                'relation' => 'viewer',
                'object' => 'document:roadmap',
            ],
            'expectation' => true,
            'contextual_tuples' => [
                [
                    'user' => 'user:bob',
                    'relation' => 'editor',
                    'object' => 'document:roadmap',
                    'condition' => ['context' => ['ip_address' => '192.168.1.1']],
                ],
                [
                    'user' => 'user:charlie',
                    'relation' => 'viewer',
                    'object' => 'document:roadmap',
                ],
            ],
        ];

        $assertion = Assertion::fromArray($data);

        // Only the tuple without condition should be included
        expect($assertion->getContextualTuples()->count())->toBe(1);
        expect($assertion->getContextualTuples()->first()->getUser())->toBe('user:charlie');
    });

    test('skips invalid contextual tuples', function (): void {
        $data = [
            'tuple_key' => [
                'user' => 'user:anne',
                'relation' => 'viewer',
                'object' => 'document:roadmap',
            ],
            'expectation' => true,
            'contextual_tuples' => [
                // Missing required fields
                ['user' => 'user:bob'],
                // Non-string values
                ['user' => 123, 'relation' => 'editor', 'object' => 'document:test'],
                ['user' => 'user:charlie', 'relation' => null, 'object' => 'document:test'],
                // Valid tuple
                ['user' => 'user:david', 'relation' => 'viewer', 'object' => 'document:valid'],
            ],
        ];

        $assertion = Assertion::fromArray($data);

        // Only the valid tuple should be included
        expect($assertion->getContextualTuples()->count())->toBe(1);
        expect($assertion->getContextualTuples()->first()->getUser())->toBe('user:david');
    });

    test('creates from array with context', function (): void {
        $context = [
            'ip_address' => '192.168.1.1',
            'time_of_day' => 'business_hours',
            'user_agent' => 'Mozilla/5.0',
        ];

        $data = [
            'tuple_key' => [
                'user' => 'user:anne',
                'relation' => 'viewer',
                'object' => 'document:roadmap',
            ],
            'expectation' => true,
            'context' => $context,
        ];

        $assertion = Assertion::fromArray($data);

        expect($assertion->getContext())->toBe($context);
    });

    test('creates from array with all optional fields', function (): void {
        $context = ['region' => 'us-east'];

        $data = [
            'tuple_key' => [
                'user' => 'user:anne',
                'relation' => 'viewer',
                'object' => 'document:roadmap',
            ],
            'expectation' => true,
            'contextual_tuples' => [
                [
                    'user' => 'group:admins#member',
                    'relation' => 'admin',
                    'object' => 'org:acme',
                ],
            ],
            'context' => $context,
        ];

        $assertion = Assertion::fromArray($data);

        expect($assertion->getTupleKey()->getUser())->toBe('user:anne');
        expect($assertion->getExpectation())->toBe(true);
        expect($assertion->getContextualTuples())->toBeInstanceOf(TupleKeys::class);
        expect($assertion->getContextualTuples()->count())->toBe(1);
        expect($assertion->getContext())->toBe($context);
    });

    test('handles empty contextual tuples array', function (): void {
        $data = [
            'tuple_key' => [
                'user' => 'user:anne',
                'relation' => 'viewer',
                'object' => 'document:roadmap',
            ],
            'expectation' => true,
            'contextual_tuples' => [],
        ];

        $assertion = Assertion::fromArray($data);

        expect($assertion->getContextualTuples())->toBeInstanceOf(TupleKeys::class);
        expect($assertion->getContextualTuples()->count())->toBe(0);
    });

    test('handles null contextual tuples', function (): void {
        $data = [
            'tuple_key' => [
                'user' => 'user:anne',
                'relation' => 'viewer',
                'object' => 'document:roadmap',
            ],
            'expectation' => true,
            'contextual_tuples' => null,
        ];

        $assertion = Assertion::fromArray($data);

        expect($assertion->getContextualTuples())->toBeNull();
    });
});
