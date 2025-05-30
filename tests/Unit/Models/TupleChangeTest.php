<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models;

use DateTimeImmutable;
use OpenFGA\Models\{Condition, TupleChange, TupleChangeInterface, TupleKey};
use OpenFGA\Models\Enums\TupleOperation;
use OpenFGA\Schema\SchemaInterface;
use ValueError;

describe('TupleChange Model', function (): void {
    test('implements TupleChangeInterface', function (): void {
        $tupleKey = new TupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1');
        $tupleChange = new TupleChange(
            tupleKey: $tupleKey,
            operation: TupleOperation::TUPLE_OPERATION_WRITE,
            timestamp: new DateTimeImmutable(),
        );

        expect($tupleChange)->toBeInstanceOf(TupleChangeInterface::class);
    });

    test('constructs with required parameters', function (): void {
        $tupleKey = new TupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1');
        $operation = TupleOperation::TUPLE_OPERATION_WRITE;
        $timestamp = new DateTimeImmutable('2024-01-01 12:00:00');

        $tupleChange = new TupleChange(
            tupleKey: $tupleKey,
            operation: $operation,
            timestamp: $timestamp,
        );

        expect($tupleChange->getTupleKey())->toBe($tupleKey);
        expect($tupleChange->getOperation())->toBe($operation);
        expect($tupleChange->getTimestamp())->toBe($timestamp);
    });

    test('handles all tuple operations', function (): void {
        $tupleKey = new TupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1');
        $timestamp = new DateTimeImmutable();

        $operations = [
            TupleOperation::TUPLE_OPERATION_WRITE,
            TupleOperation::TUPLE_OPERATION_DELETE,
        ];

        foreach ($operations as $operation) {
            $tupleChange = new TupleChange(
                tupleKey: $tupleKey,
                operation: $operation,
                timestamp: $timestamp,
            );
            expect($tupleChange->getOperation())->toBe($operation);
        }
    });

    test('serializes to JSON', function (): void {
        $tupleKey = new TupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1');
        $operation = TupleOperation::TUPLE_OPERATION_WRITE;
        $timestamp = new DateTimeImmutable('2024-01-01 12:00:00+00:00');

        $tupleChange = new TupleChange(
            tupleKey: $tupleKey,
            operation: $operation,
            timestamp: $timestamp,
        );

        $json = $tupleChange->jsonSerialize();

        expect($json)->toHaveKeys(['tuple_key', 'operation', 'timestamp']);
        expect($json['tuple_key'])->toBe([
            'user' => 'user:anne',
            'relation' => 'viewer',
            'object' => 'document:1',
        ]);
        expect($json['operation'])->toBe('TUPLE_OPERATION_WRITE');
        expect($json['timestamp'])->toBe('2024-01-01T12:00:00+00:00');
    });

    test('serializes with DELETE operation', function (): void {
        $tupleKey = new TupleKey(user: 'user:bob', relation: 'editor', object: 'document:2');
        $operation = TupleOperation::TUPLE_OPERATION_DELETE;
        $timestamp = new DateTimeImmutable('2024-01-02 15:30:00+00:00');

        $tupleChange = new TupleChange(
            tupleKey: $tupleKey,
            operation: $operation,
            timestamp: $timestamp,
        );

        $json = $tupleChange->jsonSerialize();

        expect($json['operation'])->toBe('TUPLE_OPERATION_DELETE');
    });

    test('returns schema instance', function (): void {
        $schema = TupleChange::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema->getClassName())->toBe(TupleChange::class);

        $properties = $schema->getProperties();
        expect($properties)->toHaveCount(3);

        $propertyNames = array_keys($properties);
        expect($propertyNames)->toBe(['tuple_key', 'operation', 'timestamp']);
    });

    test('schema properties have correct configuration', function (): void {
        $schema = TupleChange::schema();
        $properties = $schema->getProperties();

        // TupleKey property
        $tupleKeyProp = $properties['tuple_key'];
        expect($tupleKeyProp->name)->toBe('tuple_key');
        expect($tupleKeyProp->type)->toBe('object');
        expect($tupleKeyProp->className)->toBe(TupleKey::class);
        expect($tupleKeyProp->required)->toBe(true);

        // Operation property
        $operationProp = $properties['operation'];
        expect($operationProp->name)->toBe('operation');
        expect($operationProp->type)->toBe('string');
        expect($operationProp->required)->toBe(true);

        // Timestamp property
        $timestampProp = $properties['timestamp'];
        expect($timestampProp->name)->toBe('timestamp');
        expect($timestampProp->type)->toBe('string');
        expect($timestampProp->required)->toBe(true);
    });

    test('schema is cached', function (): void {
        $schema1 = TupleChange::schema();
        $schema2 = TupleChange::schema();

        expect($schema1)->toBe($schema2);
    });

    test('preserves tuple key with condition', function (): void {
        $condition = new Condition(
            name: 'inRegion',
            expression: 'params.region == "us-east"',
        );
        $tupleKey = new TupleKey(
            user: 'user:anne',
            relation: 'viewer',
            object: 'document:1',
            condition: $condition,
        );
        $timestamp = new DateTimeImmutable();

        $tupleChange = new TupleChange(
            tupleKey: $tupleKey,
            operation: TupleOperation::TUPLE_OPERATION_WRITE,
            timestamp: $timestamp,
        );

        expect($tupleChange->getTupleKey()->getCondition())->toBe($condition);

        $json = $tupleChange->jsonSerialize();
        expect($json['tuple_key'])->toHaveKey('condition');
    });

    test('handles different timestamp formats', function (): void {
        $tupleKey = new TupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1');

        // Test with different timezone
        $timestamp1 = new DateTimeImmutable('2024-01-01 12:00:00+05:00');
        $tupleChange1 = new TupleChange(
            tupleKey: $tupleKey,
            operation: TupleOperation::TUPLE_OPERATION_WRITE,
            timestamp: $timestamp1,
        );

        $json1 = $tupleChange1->jsonSerialize();
        // The timestamp is normalized to UTC, so it will be 07:00:00+00:00
        expect($json1['timestamp'])->toMatch('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\+00:00$/');

        // Test with UTC
        $timestamp2 = new DateTimeImmutable('2024-01-01 12:00:00Z');
        $tupleChange2 = new TupleChange(
            tupleKey: $tupleKey,
            operation: TupleOperation::TUPLE_OPERATION_DELETE,
            timestamp: $timestamp2,
        );

        $json2 = $tupleChange2->jsonSerialize();
        expect($json2['timestamp'])->toMatch('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\+00:00$/');
    });

    test('maintains immutability', function (): void {
        $tupleKey = new TupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1');
        $operation = TupleOperation::TUPLE_OPERATION_WRITE;
        $timestamp = new DateTimeImmutable();

        $tupleChange = new TupleChange(
            tupleKey: $tupleKey,
            operation: $operation,
            timestamp: $timestamp,
        );

        // Verify that the same instances are returned
        expect($tupleChange->getTupleKey())->toBe($tupleKey);
        expect($tupleChange->getOperation())->toBe($operation);
        expect($tupleChange->getTimestamp())->toBe($timestamp);
    });

    test('creates from array with write operation', function (): void {
        $tupleKey = new TupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1');
        $timestamp = new DateTimeImmutable('2024-01-01 12:00:00');

        $data = [
            'tuple_key' => $tupleKey,
            'operation' => 'TUPLE_OPERATION_WRITE',
            'timestamp' => $timestamp,
        ];

        $tupleChange = TupleChange::fromArray($data);

        expect($tupleChange)->toBeInstanceOf(TupleChange::class);
        expect($tupleChange->getTupleKey())->toBe($tupleKey);
        expect($tupleChange->getOperation())->toBe(TupleOperation::TUPLE_OPERATION_WRITE);
        expect($tupleChange->getTimestamp())->toBe($timestamp);
    });

    test('creates from array with delete operation', function (): void {
        $tupleKey = new TupleKey(user: 'user:bob', relation: 'editor', object: 'document:2');
        $timestamp = new DateTimeImmutable('2024-01-02 15:30:00');

        $data = [
            'tuple_key' => $tupleKey,
            'operation' => 'TUPLE_OPERATION_DELETE',
            'timestamp' => $timestamp,
        ];

        $tupleChange = TupleChange::fromArray($data);

        expect($tupleChange)->toBeInstanceOf(TupleChange::class);
        expect($tupleChange->getTupleKey())->toBe($tupleKey);
        expect($tupleChange->getOperation())->toBe(TupleOperation::TUPLE_OPERATION_DELETE);
        expect($tupleChange->getTimestamp())->toBe($timestamp);
    });

    test('fromArray preserves tuple key with condition', function (): void {
        $condition = new Condition(
            name: 'inRegion',
            expression: 'params.region == "us-east"',
        );
        $tupleKey = new TupleKey(
            user: 'user:anne',
            relation: 'viewer',
            object: 'document:1',
            condition: $condition,
        );
        $timestamp = new DateTimeImmutable('2024-01-01 12:00:00');

        $data = [
            'tuple_key' => $tupleKey,
            'operation' => 'TUPLE_OPERATION_WRITE',
            'timestamp' => $timestamp,
        ];

        $tupleChange = TupleChange::fromArray($data);

        expect($tupleChange->getTupleKey())->toBe($tupleKey);
        expect($tupleChange->getTupleKey()->getCondition())->toBe($condition);
    });

    test('fromArray throws error on invalid operation', function (): void {
        $tupleKey = new TupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1');
        $timestamp = new DateTimeImmutable();

        $data = [
            'tuple_key' => $tupleKey,
            'operation' => 'INVALID_OPERATION',
            'timestamp' => $timestamp,
        ];

        expect(fn () => TupleChange::fromArray($data))->toThrow(ValueError::class);
    });

    test('has correct OpenAPI type constant', function (): void {
        expect(TupleChange::OPENAPI_TYPE)->toBe('TupleChange');
    });
});
