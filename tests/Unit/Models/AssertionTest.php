<?php

namespace OpenFGATests\Unit\Models;

use OpenFGA\Models\Assertion;
use OpenFGA\Models\AssertionTupleKey;
use OpenFGA\Models\AssertionTupleKeyInterface;
use OpenFGA\Models\Collections\TupleKeys;
use OpenFGA\Models\Collections\TupleKeysInterface;
use OpenFGA\Schema\SchemaInterface;
use JsonSerializable;
use ArrayIterator;
use Countable;
use IteratorAggregate;

// Dummy Interfaces & Classes for AssertionTest

if (!interface_exists(AssertionTupleKeyInterface::class)) {
    interface AssertionTupleKeyInterface extends JsonSerializable {
        public function getUser(): string;
        public function getRelation(): string;
        public function getObject(): string;
    }
}

class DummyAssertionTupleKey implements AssertionTupleKeyInterface {
    public function __construct(
        private string $user = 'user:anne',
        private string $relation = 'viewer',
        private string $object = 'document:123'
    ) {
    }

    public function getUser(): string {
        return $this->user;
    }

    public function getRelation(): string {
        return $this->relation;
    }

    public function getObject(): string {
        return $this->object;
    }

    public function jsonSerialize(): array {
        return [
            'user' => $this->user,
            'relation' => $this->relation,
            'object' => $this->object,
        ];
    }
}

if (!interface_exists(TupleKeysInterface::class)) {
    interface TupleKeysInterface extends JsonSerializable, Countable, IteratorAggregate {
        // Define methods based on actual interface
    }
}

class DummyTupleKeys implements TupleKeysInterface {
    private array $keys;

    public function __construct(array $keys = []) {
        if (empty($keys)) {
            // Provide a default if empty, matching the example structure
            $this->keys = [['user' => 'user:bob', 'relation' => 'editor', 'object' => 'folder:abc']];
        } else {
            $this->keys = $keys;
        }
    }

    public function jsonSerialize(): array {
        return $this->keys;
    }

    public function count(): int {
        return count($this->keys);
    }

    public function getIterator(): ArrayIterator {
        return new ArrayIterator($this->keys);
    }
}

describe('Assertion', function () {
    describe('constructor', function () {
        it('constructs with required tupleKey and expectation', function () {
            $tupleKey = new DummyAssertionTupleKey();
            $assertion = new Assertion(tupleKey: $tupleKey, expectation: true);

            expect($assertion->getTupleKey())->toBe($tupleKey)
                ->and($assertion->getExpectation())->toBeTrue()
                ->and($assertion->getContextualTuples())->toBeNull()
                ->and($assertion->getContext())->toBeNull();
        });

        it('constructs with all parameters', function () {
            $tupleKey = new DummyAssertionTupleKey('u1', 'r1', 'o1');
            $contextualTuples = new DummyTupleKeys([['user' => 'u2', 'relation' => 'r2', 'object' => 'o2']]);
            $context = ['ip_address' => '127.0.0.1'];
            $assertion = new Assertion(
                tupleKey: $tupleKey,
                expectation: false,
                contextualTuples: $contextualTuples,
                context: $context
            );

            expect($assertion->getTupleKey())->toBe($tupleKey)
                ->and($assertion->getExpectation())->toBeFalse()
                ->and($assertion->getContextualTuples())->toBe($contextualTuples)
                ->and($assertion->getContext())->toBe($context);
        });
    });

    describe('getters', function () {
        $tupleKey = new DummyAssertionTupleKey();
        $contextualTuples = new DummyTupleKeys();
        $context = ['param' => 'value'];
        $assertion = new Assertion($tupleKey, true, $contextualTuples, $context);
        $assertionRequiredOnly = new Assertion(new DummyAssertionTupleKey('u', 'r', 'o'), false);

        it('getTupleKey returns the correct value', function () use ($assertion, $tupleKey) {
            expect($assertion->getTupleKey())->toBe($tupleKey);
        });

        it('getExpectation returns the correct value', function () use ($assertion) {
            expect($assertion->getExpectation())->toBeTrue();
        });

        it('getContextualTuples returns the correct value or null', function () use ($assertion, $contextualTuples, $assertionRequiredOnly) {
            expect($assertion->getContextualTuples())->toBe($contextualTuples)
                ->and($assertionRequiredOnly->getContextualTuples())->toBeNull();
        });

        it('getContext returns the correct value or null', function () use ($assertion, $context, $assertionRequiredOnly) {
            expect($assertion->getContext())->toBe($context)
                ->and($assertionRequiredOnly->getContext())->toBeNull();
        });
    });

    describe('jsonSerialize', function () {
        it('serializes with only required fields', function () {
            $tupleKey = new DummyAssertionTupleKey('user:charlie', 'can_read', 'report:secret');
            $assertion = new Assertion(tupleKey: $tupleKey, expectation: true);
            expect($assertion->jsonSerialize())->toBe([
                'tuple_key' => [
                    'user' => 'user:charlie',
                    'relation' => 'can_read',
                    'object' => 'report:secret',
                ],
                'expectation' => true,
            ]);
        });

        it('serializes with all fields', function () {
            $tupleKey = new DummyAssertionTupleKey('u1', 'r1', 'o1');
            $contextualTuplesData = [['user' => 'u2', 'relation' => 'r2', 'object' => 'o2']];
            $contextualTuples = new DummyTupleKeys($contextualTuplesData);
            $context = ['request_id' => 'xyz789'];
            $assertion = new Assertion(
                tupleKey: $tupleKey,
                expectation: false,
                contextualTuples: $contextualTuples,
                context: $context
            );

            expect($assertion->jsonSerialize())->toBe([
                'tuple_key' => $tupleKey->jsonSerialize(),
                'expectation' => false,
                'contextual_tuples' => $contextualTuplesData,
                'context' => $context,
            ]);
        });
    });

    describe('static schema()', function () {
        $schema = Assertion::schema();

        it('returns a SchemaInterface instance', function () use ($schema) {
            expect($schema)->toBeInstanceOf(SchemaInterface::class);
        });

        it('has the correct className', function () use ($schema) {
            expect($schema->getClassName())->toBe(Assertion::class);
        });

        it('has "tuple_key" property defined correctly', function () use ($schema) {
            $properties = $schema->getProperties();
            expect($properties)->toHaveKey('tuple_key');
            $prop = $properties['tuple_key'];
            expect($prop->getName())->toBe('tuple_key')
                ->and($prop->getTypes())->toBe([AssertionTupleKey::class])
                ->and($prop->isRequired())->toBeTrue();
        });

        it('has "expectation" property defined correctly', function () use ($schema) {
            $properties = $schema->getProperties();
            expect($properties)->toHaveKey('expectation');
            $prop = $properties['expectation'];
            expect($prop->getName())->toBe('expectation')
                ->and($prop->getTypes())->toBe(['boolean']) // Based on typical use, should be bool
                ->and($prop->isRequired())->toBeTrue();
        });

        it('has "contextual_tuples" property defined correctly', function () use ($schema) {
            $properties = $schema->getProperties();
            expect($properties)->toHaveKey('contextual_tuples');
            $prop = $properties['contextual_tuples'];
            expect($prop->getName())->toBe('contextual_tuples')
                ->and($prop->getTypes())->toBe([TupleKeys::class])
                ->and($prop->isRequired())->toBeFalse();
        });

        it('has "context" property defined correctly', function () use ($schema) {
            $properties = $schema->getProperties();
            expect($properties)->toHaveKey('context');
            $prop = $properties['context'];
            expect($prop->getName())->toBe('context')
                ->and($prop->getTypes())->toBe(['array'])
                ->and($prop->isRequired())->toBeFalse();
        });
    });
});

?>
