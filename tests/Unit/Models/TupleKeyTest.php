<?php

namespace OpenFGATests\Unit\Models;

use OpenFGA\Models\TupleKey;
use OpenFGA\Models\Condition;
use OpenFGA\Models\ConditionInterface;
use OpenFGA\Models\Collections\ConditionParametersInterface;
use OpenFGA\Schema\SchemaInterface;

// DummyCondition for testing
class DummyCondition implements ConditionInterface {
    public function __construct(private string $name = 'dummy_condition', private ?array $context = null) {
    }

    public function getName(): string {
        return $this->name;
    }

    public function getContext(): ?array {
        return $this->context;
    }

    public function getParameters(): ?ConditionParametersInterface {
        return null; // Not needed for these tests
    }

    public function jsonSerialize(): array {
        $data = ['name' => $this->name];
        if ($this->context !== null) {
            $data['context'] = $this->context;
        }
        return $data;
    }
}

describe('TupleKey', function () {
    describe('constructor', function () {
        it('constructs with all parameters including condition', function () {
            $condition = new DummyCondition(name: 'has_context', context: ['param' => 'value']);
            $tupleKey = new TupleKey(
                user: 'user:anne',
                relation: 'viewer',
                object: 'document:123',
                condition: $condition
            );
            expect($tupleKey->getUser())->toBe('user:anne')
                ->and($tupleKey->getRelation())->toBe('viewer')
                ->and($tupleKey->getObject())->toBe('document:123')
                ->and($tupleKey->getCondition())->toBe($condition);
        });

        it('constructs with condition parameter omitted', function () {
            $tupleKey = new TupleKey(
                user: 'user:bob',
                relation: 'editor',
                object: 'folder:abc'
            );
            expect($tupleKey->getUser())->toBe('user:bob')
                ->and($tupleKey->getRelation())->toBe('editor')
                ->and($tupleKey->getObject())->toBe('folder:abc')
                ->and($tupleKey->getCondition())->toBeNull();
        });
    });

    describe('getters', function () {
        $condition = new DummyCondition();
        $tupleKeyWithCondition = new TupleKey('u1', 'r1', 'o1', $condition);
        $tupleKeyWithoutCondition = new TupleKey('u2', 'r2', 'o2');

        it('getUser returns the correct value', function () use ($tupleKeyWithCondition) {
            expect($tupleKeyWithCondition->getUser())->toBe('u1');
        });

        it('getRelation returns the correct value', function () use ($tupleKeyWithCondition) {
            expect($tupleKeyWithCondition->getRelation())->toBe('r1');
        });

        it('getObject returns the correct value', function () use ($tupleKeyWithCondition) {
            expect($tupleKeyWithCondition->getObject())->toBe('o1');
        });

        it('getCondition returns the condition when set', function () use ($tupleKeyWithCondition, $condition) {
            expect($tupleKeyWithCondition->getCondition())->toBe($condition);
        });

        it('getCondition returns null when not set', function () use ($tupleKeyWithoutCondition) {
            expect($tupleKeyWithoutCondition->getCondition())->toBeNull();
        });
    });

    describe('jsonSerialize', function () {
        it('serializes with condition set', function () {
            $condition = new DummyCondition(name: 'test_cond', context: ['ip' => '127.0.0.1']);
            $tupleKey = new TupleKey(
                user: 'user:anne',
                relation: 'can_view',
                object: 'report:xyz',
                condition: $condition
            );
            expect($tupleKey->jsonSerialize())->toBe([
                'user' => 'user:anne',
                'relation' => 'can_view',
                'object' => 'report:xyz',
                'condition' => [
                    'name' => 'test_cond',
                    'context' => ['ip' => '127.0.0.1'],
                ],
            ]);
        });

        it('serializes without condition when it is null', function () {
            $tupleKey = new TupleKey(
                user: 'user:bob',
                relation: 'owner',
                object: 'document:secret'
            );
            expect($tupleKey->jsonSerialize())->toBe([
                'user' => 'user:bob',
                'relation' => 'owner',
                'object' => 'document:secret',
            ]);
        });
    });

    describe('schema', function () {
        $schema = TupleKey::schema();

        it('returns a SchemaInterface instance', function () use ($schema) {
            expect($schema)->toBeInstanceOf(SchemaInterface::class);
        });

        it('has the correct className', function () use ($schema) {
            expect($schema->getClassName())->toBe(TupleKey::class);
        });

        it('has "user", "relation", "object" properties defined correctly as required strings', function () use ($schema) {
            $properties = $schema->getProperties();
            expect($properties)->toHaveKeys(['user', 'relation', 'object']);

            $userProp = $properties['user'];
            expect($userProp->getName())->toBe('user')
                ->and($userProp->getTypes())->toBe(['string'])
                ->and($userProp->isRequired())->toBeTrue();

            $relationProp = $properties['relation'];
            expect($relationProp->getName())->toBe('relation')
                ->and($relationProp->getTypes())->toBe(['string'])
                ->and($relationProp->isRequired())->toBeTrue();

            $objectProp = $properties['object'];
            expect($objectProp->getName())->toBe('object')
                ->and($objectProp->getTypes())->toBe(['string'])
                ->and($objectProp->isRequired())->toBeTrue();
        });

        it('has the "condition" property defined correctly', function () use ($schema) {
            $properties = $schema->getProperties();
            expect($properties)->toHaveKey('condition');
            $conditionProp = $properties['condition'];
            expect($conditionProp->getName())->toBe('condition')
                ->and($conditionProp->getTypes())->toBe([Condition::class])
                ->and($conditionProp->isRequired())->toBeFalse();
        });
    });
});

?>
