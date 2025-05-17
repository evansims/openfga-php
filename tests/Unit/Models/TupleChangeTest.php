<?php

declare(strict_types=1);

use OpenFGA\Models\{TupleChange, TupleKey, TupleOperation};

test('constructor and getters', function (): void {
    $tupleKey = new TupleKey('document:1', 'reader', 'user:1');
    $operation = TupleOperation::TUPLE_OPERATION_WRITE;
    $timestamp = new DateTimeImmutable('2023-01-01T12:00:00+00:00');

    $tupleChange = new TupleChange($tupleKey, $operation, $timestamp);

    expect($tupleChange->getTupleKey())->toBe($tupleKey)
        ->and($tupleChange->getOperation())->toBe($operation)
        ->and($tupleChange->getTimestamp())->toEqual($timestamp);
});

test('json serialize with write operation', function (): void {
    $tupleKey = new TupleKey('document:1', 'reader', 'user:1');
    $operation = TupleOperation::TUPLE_OPERATION_WRITE;
    $timestamp = new DateTimeImmutable('2023-01-01T12:00:00+05:00'); // +05:00 timezone

    $tupleChange = new TupleChange($tupleKey, $operation, $timestamp);

    $result = $tupleChange->jsonSerialize();

    expect($result)->toBe([
        'tuple_key' => $tupleKey->jsonSerialize(),
        'operation' => 'TUPLE_OPERATION_WRITE',
        'timestamp' => '2023-01-01T07:00:00+00:00', // Should be converted to UTC
    ]);
});

test('json serialize with delete operation', function (): void {
    $tupleKey = new TupleKey('document:1', 'reader', 'user:1');
    $operation = TupleOperation::TUPLE_OPERATION_DELETE;
    $timestamp = new DateTimeImmutable('2023-01-01T12:00:00-05:00'); // -05:00 timezone

    $tupleChange = new TupleChange($tupleKey, $operation, $timestamp);

    $result = $tupleChange->jsonSerialize();

    expect($result)->toBe([
        'tuple_key' => $tupleKey->jsonSerialize(),
        'operation' => 'TUPLE_OPERATION_DELETE',
        'timestamp' => '2023-01-01T17:00:00+00:00', // Should be converted to UTC
    ]);
});

test('schema', function (): void {
    $schema = TupleChange::schema();

    expect($schema->getClassName())->toBe(TupleChange::class)
        ->and($schema->getProperties())->toHaveCount(3)
        ->and($schema->getProperty('tuple_key')->name)->toBe('tuple_key')
        ->and($schema->getProperty('tuple_key')->type)->toBe('OpenFGA\\Models\\TupleKey')
        ->and($schema->getProperty('tuple_key')->required)->toBeTrue()
        ->and($schema->getProperty('operation')->name)->toBe('operation')
        ->and($schema->getProperty('operation')->type)->toBe('OpenFGA\\Models\\TupleOperation')
        ->and($schema->getProperty('operation')->required)->toBeTrue()
        ->and($schema->getProperty('timestamp')->name)->toBe('timestamp')
        ->and($schema->getProperty('timestamp')->type)->toBe('string')
        ->and($schema->getProperty('timestamp')->format)->toBe('date-time')
        ->and($schema->getProperty('timestamp')->required)->toBeTrue();
});
