<?php

declare(strict_types=1);

use OpenFGA\Models\Collections\TupleChanges;
use OpenFGA\Models\Enums\TupleOperation;
use OpenFGA\Models\{TupleChange, TupleKey};
use OpenFGA\Responses\{ListTupleChangesResponse, ListTupleChangesResponseInterface};
use OpenFGA\Schema\SchemaInterface;

test('ListTupleChangesResponse implements ListTupleChangesResponseInterface', function (): void {
    $changes = new TupleChanges([]);
    $response = new ListTupleChangesResponse($changes, null);

    expect($response)->toBeInstanceOf(ListTupleChangesResponseInterface::class);
});

test('ListTupleChangesResponse constructs with changes and continuation token', function (): void {
    $tupleKey = new TupleKey(user: 'user:alice', relation: 'viewer', object: 'document:readme');
    $tupleChange = new TupleChange(
        tupleKey: $tupleKey,
        operation: TupleOperation::TUPLE_OPERATION_WRITE,
        timestamp: new DateTimeImmutable('2024-01-01T10:00:00Z'),
    );
    $changes = new TupleChanges([$tupleChange]);
    $continuationToken = 'token-123';

    $response = new ListTupleChangesResponse($changes, $continuationToken);

    expect($response->getChanges())->toBe($changes);
    expect($response->getContinuationToken())->toBe($continuationToken);
});

test('ListTupleChangesResponse constructs with null continuation token', function (): void {
    $changes = new TupleChanges([]);
    $response = new ListTupleChangesResponse($changes, null);

    expect($response->getChanges())->toBe($changes);
    expect($response->getContinuationToken())->toBeNull();
});

test('ListTupleChangesResponse constructs with empty changes collection', function (): void {
    $changes = new TupleChanges([]);
    $response = new ListTupleChangesResponse($changes, null);

    expect($response->getChanges())->toBeInstanceOf(TupleChanges::class);
    expect($response->getChanges())->toHaveCount(0);
});

test('ListTupleChangesResponse schema returns correct structure', function (): void {
    $schema = ListTupleChangesResponse::schema();

    expect($schema)->toBeInstanceOf(SchemaInterface::class);
    expect($schema->getClassName())->toBe(ListTupleChangesResponse::class);

    $properties = $schema->getProperties();
    expect($properties)->toHaveCount(2);
    expect($properties)->toHaveKeys(['changes', 'continuation_token']);

    expect($properties['changes']->name)->toBe('changes');
    expect($properties['changes']->type)->toBe(TupleChanges::class);
    expect($properties['changes']->required)->toBeTrue();

    expect($properties['continuation_token']->name)->toBe('continuation_token');
    expect($properties['continuation_token']->type)->toBe('string');
    expect($properties['continuation_token']->required)->toBeFalse();
});

test('ListTupleChangesResponse schema is cached', function (): void {
    $schema1 = ListTupleChangesResponse::schema();
    $schema2 = ListTupleChangesResponse::schema();

    expect($schema1)->toBe($schema2);
});

// Note: fromResponse method testing would require integration tests due to SchemaValidator complexity
// These tests focus on the model's direct functionality

test('ListTupleChangesResponse handles response data without continuation token', function (): void {
    $changes = new TupleChanges([]);
    $response = new ListTupleChangesResponse($changes, null);

    expect($response)->toBeInstanceOf(ListTupleChangesResponseInterface::class);
    expect($response->getContinuationToken())->toBeNull();
    expect($response->getChanges())->toBeInstanceOf(TupleChanges::class);
});

// Removed fromResponse error handling test - handled in integration tests

// Removed fromResponse validation error test - handled in integration tests

test('ListTupleChangesResponse handles large continuation tokens', function (): void {
    $largeToken = str_repeat('a', 1000);
    $changes = new TupleChanges([]);
    $response = new ListTupleChangesResponse($changes, $largeToken);

    expect($response->getContinuationToken())->toBe($largeToken);
    expect(\strlen($response->getContinuationToken()))->toBe(1000);
});

test('ListTupleChangesResponse handles special characters in continuation token', function (): void {
    $specialToken = 'token+with/special=chars&more%encoded';
    $changes = new TupleChanges([]);
    $response = new ListTupleChangesResponse($changes, $specialToken);

    expect($response->getContinuationToken())->toBe($specialToken);
});

test('ListTupleChangesResponse handles empty string continuation token', function (): void {
    $changes = new TupleChanges([]);
    $response = new ListTupleChangesResponse($changes, '');

    expect($response->getContinuationToken())->toBe('');
});
