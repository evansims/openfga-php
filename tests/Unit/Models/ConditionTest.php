<?php

namespace OpenFGATests\Unit\Models;

use OpenFGA\Models\Condition;
use OpenFGA\Models\Collections\ConditionParameters;
use OpenFGA\Models\Collections\ConditionParametersInterface;
use OpenFGA\Models\ConditionMetadata;
use OpenFGA\Models\ConditionMetadataInterface;
use OpenFGA\Schema\SchemaInterface;
use JsonSerializable;
use ArrayIterator;
use Countable;
use IteratorAggregate;

// Dummy Interfaces & Classes for ConditionTest

if (!interface_exists(ConditionParametersInterface::class)) {
    interface ConditionParametersInterface extends JsonSerializable, Countable, IteratorAggregate {
        // Define methods based on actual interface
    }
}

class DummyConditionParameters implements ConditionParametersInterface {
    private array $parameters;

    public function __construct(array $parameters = ['param_key' => ['type' => 'string']]) {
        $this->parameters = $parameters;
    }

    public function jsonSerialize(): array {
        return $this->parameters;
    }

    public function count(): int {
        return count($this->parameters);
    }

    public function getIterator(): ArrayIterator {
        return new ArrayIterator($this->parameters);
    }
}

if (!interface_exists(ConditionMetadataInterface::class)) {
    interface ConditionMetadataInterface extends JsonSerializable {
        // Define methods based on actual interface
    }
}

class DummyConditionMetadata implements ConditionMetadataInterface {
    private array $metadata;

    public function __construct(array $metadata = ['source_info' => 'test_source']) {
        $this->metadata = $metadata;
    }

    public function jsonSerialize(): array {
        return $this->metadata;
    }
}

describe('Condition', function () {
    describe('constructor', function () {
        it('constructs with required name and expression', function () {
            $condition = new Condition(name: 'is_public', expression: 'object.public == true');
            expect($condition->getName())->toBe('is_public')
                ->and($condition->getExpression())->toBe('object.public == true')
                ->and($condition->getParameters())->toBeNull()
                ->and($condition->getMetadata())->toBeNull();
        });

        it('constructs with name, expression, and parameters', function () {
            $params = new DummyConditionParameters(['ip_address' => ['type' => 'string']]);
            $condition = new Condition(
                name: 'ip_match',
                expression: 'request.ip == params.ip_address',
                parameters: $params
            );
            expect($condition->getName())->toBe('ip_match')
                ->and($condition->getExpression())->toBe('request.ip == params.ip_address')
                ->and($condition->getParameters())->toBe($params)
                ->and($condition->getMetadata())->toBeNull();
        });

        it('constructs with name, expression, and metadata', function () {
            $metadata = new DummyConditionMetadata(['description' => 'Checks for public access']);
            $condition = new Condition(
                name: 'is_public_v2',
                expression: 'object.attributes.public',
                metadata: $metadata
            );
            expect($condition->getName())->toBe('is_public_v2')
                ->and($condition->getExpression())->toBe('object.attributes.public')
                ->and($condition->getParameters())->toBeNull()
                ->and($condition->getMetadata())->toBe($metadata);
        });

        it('constructs with all parameters set', function () {
            $params = new DummyConditionParameters();
            $metadata = new DummyConditionMetadata();
            $condition = new Condition(
                name: 'complex_condition',
                expression: 'user.id == params.user_id && resource.owner == user.id',
                parameters: $params,
                metadata: $metadata
            );
            expect($condition->getName())->toBe('complex_condition')
                ->and($condition->getExpression())->toBe('user.id == params.user_id && resource.owner == user.id')
                ->and($condition->getParameters())->toBe($params)
                ->and($condition->getMetadata())->toBe($metadata);
        });
    });

    describe('getters', function () {
        $params = new DummyConditionParameters();
        $metadata = new DummyConditionMetadata();
        $conditionWithAll = new Condition('c1', 'e1', $params, $metadata);
        $conditionRequiredOnly = new Condition('c2', 'e2');

        it('getName returns the correct value', function () use ($conditionWithAll, $conditionRequiredOnly) {
            expect($conditionWithAll->getName())->toBe('c1')
                ->and($conditionRequiredOnly->getName())->toBe('c2');
        });

        it('getExpression returns the correct value', function () use ($conditionWithAll, $conditionRequiredOnly) {
            expect($conditionWithAll->getExpression())->toBe('e1')
                ->and($conditionRequiredOnly->getExpression())->toBe('e2');
        });

        it('getParameters returns the correct value or null', function () use ($conditionWithAll, $params, $conditionRequiredOnly) {
            expect($conditionWithAll->getParameters())->toBe($params)
                ->and($conditionRequiredOnly->getParameters())->toBeNull();
        });

        it('getMetadata returns the correct value or null', function () use ($conditionWithAll, $metadata, $conditionRequiredOnly) {
            expect($conditionWithAll->getMetadata())->toBe($metadata)
                ->and($conditionRequiredOnly->getMetadata())->toBeNull();
        });
    });

    describe('jsonSerialize', function () {
        it('serializes with only name and expression', function () {
            $condition = new Condition(name: 'is_true', expression: 'true');
            expect($condition->jsonSerialize())->toBe([
                'name' => 'is_true',
                'expression' => 'true',
            ]);
        });

        it('serializes with parameters set', function () {
            $paramsData = ['user_id' => ['type' => 'string']];
            $params = new DummyConditionParameters($paramsData);
            $condition = new Condition(name: 'user_check', expression: 'subject.id == params.user_id', parameters: $params);
            expect($condition->jsonSerialize())->toBe([
                'name' => 'user_check',
                'expression' => 'subject.id == params.user_id',
                'parameters' => $paramsData,
            ]);
        });

        it('serializes with metadata set', function () {
            $metadataData = ['audit_log' => 'true'];
            $metadata = new DummyConditionMetadata($metadataData);
            $condition = new Condition(name: 'audited_op', expression: 'resource.audited == true', metadata: $metadata);
            expect($condition->jsonSerialize())->toBe([
                'name' => 'audited_op',
                'expression' => 'resource.audited == true',
                'metadata' => $metadataData,
            ]);
        });

        it('serializes with all parameters set', function () {
            $paramsData = ['allowed_actions' => ['type' => 'list[string]']];
            $metadataData = ['version' => '2.1'];
            $params = new DummyConditionParameters($paramsData);
            $metadata = new DummyConditionMetadata($metadataData);
            $condition = new Condition(
                name: 'action_allowed',
                expression: 'request.action in params.allowed_actions',
                parameters: $params,
                metadata: $metadata
            );
            expect($condition->jsonSerialize())->toBe([
                'name' => 'action_allowed',
                'expression' => 'request.action in params.allowed_actions',
                'parameters' => $paramsData,
                'metadata' => $metadataData,
            ]);
        });
    });

    describe('static schema()', function () {
        $schema = Condition::schema();

        it('returns a SchemaInterface instance', function () use ($schema) {
            expect($schema)->toBeInstanceOf(SchemaInterface::class);
        });

        it('has the correct className', function () use ($schema) {
            expect($schema->getClassName())->toBe(Condition::class);
        });

        it('has "name" property defined correctly', function () use ($schema) {
            $properties = $schema->getProperties();
            expect($properties)->toHaveKey('name');
            $prop = $properties['name'];
            expect($prop->getName())->toBe('name')
                ->and($prop->getTypes())->toBe(['string'])
                ->and($prop->isRequired())->toBeTrue();
        });

        it('has "expression" property defined correctly', function () use ($schema) {
            $properties = $schema->getProperties();
            expect($properties)->toHaveKey('expression');
            $prop = $properties['expression'];
            expect($prop->getName())->toBe('expression')
                ->and($prop->getTypes())->toBe(['string'])
                ->and($prop->isRequired())->toBeTrue();
        });

        it('has "parameters" property defined correctly', function () use ($schema) {
            $properties = $schema->getProperties();
            expect($properties)->toHaveKey('parameters');
            $prop = $properties['parameters'];
            expect($prop->getName())->toBe('parameters')
                ->and($prop->getTypes())->toBe([ConditionParameters::class])
                ->and($prop->isRequired())->toBeFalse();
        });

        it('has "metadata" property defined correctly', function () use ($schema) {
            $properties = $schema->getProperties();
            expect($properties)->toHaveKey('metadata');
            $prop = $properties['metadata'];
            expect($prop->getName())->toBe('metadata')
                ->and($prop->getTypes())->toBe([ConditionMetadata::class])
                ->and($prop->isRequired())->toBeFalse();
        });
    });
});

?>
