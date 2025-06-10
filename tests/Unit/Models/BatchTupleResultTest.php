<?php

declare(strict_types=1);

use OpenFGA\Exceptions\ClientError;
use OpenFGA\Models\{BatchTupleResult, BatchTupleResultInterface};
use OpenFGA\Schemas\SchemaInterface;

beforeEach(function (): void {
    $this->responses = ['response1', 'response2'];
    $this->errors = [
        new Exception('First error'),
        new RuntimeException('Second error'),
    ];
});

it('implements BatchTupleResultInterface', function (): void {
    $result = new BatchTupleResult(10, 2, 1, 1);

    expect($result)->toBeInstanceOf(BatchTupleResultInterface::class);
});

it('returns correct basic properties', function (): void {
    $result = new BatchTupleResult(
        totalOperations: 100,
        totalChunks: 5,
        successfulChunks: 3,
        failedChunks: 2,
        responses: $this->responses,
        errors: $this->errors,
    );

    expect($result->getTotalOperations())->toBe(100);
    expect($result->getTotalChunks())->toBe(5);
    expect($result->getSuccessfulChunks())->toBe(3);
    expect($result->getFailedChunks())->toBe(2);
    expect($result->getResponses())->toBe($this->responses);
    expect($result->getErrors())->toBe($this->errors);
});

it('calculates success rate correctly', function (): void {
    $result = new BatchTupleResult(100, 4, 3, 1);
    expect($result->getSuccessRate())->toBe(0.75);

    $perfectResult = new BatchTupleResult(100, 2, 2, 0);
    expect($perfectResult->getSuccessRate())->toBe(1.0);

    $failedResult = new BatchTupleResult(100, 3, 0, 3);
    expect($failedResult->getSuccessRate())->toBe(0.0);
});

it('handles zero total chunks for success rate', function (): void {
    $result = new BatchTupleResult(0, 0, 0, 0);
    expect($result->getSuccessRate())->toBe(0.0);
});

it('identifies complete success correctly', function (): void {
    $completeSuccess = new BatchTupleResult(100, 3, 3, 0);
    expect($completeSuccess->isCompleteSuccess())->toBeTrue();
    expect($completeSuccess->isCompleteFailure())->toBeFalse();
    expect($completeSuccess->isPartialSuccess())->toBeFalse();
});

it('identifies complete failure correctly', function (): void {
    $completeFailure = new BatchTupleResult(100, 3, 0, 3);
    expect($completeFailure->isCompleteFailure())->toBeTrue();
    expect($completeFailure->isCompleteSuccess())->toBeFalse();
    expect($completeFailure->isPartialSuccess())->toBeFalse();
});

it('identifies partial success correctly', function (): void {
    $partialSuccess = new BatchTupleResult(100, 4, 2, 2);
    expect($partialSuccess->isPartialSuccess())->toBeTrue();
    expect($partialSuccess->isCompleteSuccess())->toBeFalse();
    expect($partialSuccess->isCompleteFailure())->toBeFalse();
});

it('handles edge case with zero chunks', function (): void {
    $noChunks = new BatchTupleResult(0, 0, 0, 0);
    expect($noChunks->isCompleteSuccess())->toBeFalse();
    expect($noChunks->isCompleteFailure())->toBeFalse();
    expect($noChunks->isPartialSuccess())->toBeFalse();
});

it('returns first error when errors exist', function (): void {
    $result = new BatchTupleResult(100, 2, 1, 1, [], $this->errors);
    expect($result->getFirstError())->toBe($this->errors[0]);
});

it('returns null when no errors exist', function (): void {
    $result = new BatchTupleResult(100, 2, 2, 0);
    expect($result->getFirstError())->toBeNull();
});

it('returns null for first error when errors array is empty', function (): void {
    $result = new BatchTupleResult(100, 2, 1, 1, [], []);
    expect($result->getFirstError())->toBeNull();
});

it('does not throw when no failures occurred', function (): void {
    $successResult = new BatchTupleResult(100, 2, 2, 0);

    // This should not throw, so we just call it
    $successResult->throwOnFailure();

    // If we reach here, it didn't throw
    expect(true)->toBeTrue();
});

it('throws ClientThrowable when first error is ClientThrowable', function (): void {
    $clientError = ClientError::Validation->exception(context: ['message' => 'Client error']);
    $result = new BatchTupleResult(100, 2, 1, 1, [], [$clientError]);

    expect(fn () => $result->throwOnFailure())->toThrow($clientError::class);
});

it('throws generic Throwable when first error is not ClientThrowable', function (): void {
    $genericError = new RuntimeException('Generic error');
    $result = new BatchTupleResult(100, 2, 1, 1, [], [$genericError]);

    expect(fn () => $result->throwOnFailure())->toThrow(RuntimeException::class, 'Generic error');
});

it('throws RuntimeException when no first error but has failed chunks', function (): void {
    $result = new BatchTupleResult(100, 2, 1, 1, [], []);

    expect(fn () => $result->throwOnFailure())
        ->toThrow(RuntimeException::class, 'Batch operation failed: 1 of 2 chunks failed');
});

it('serializes to JSON correctly', function (): void {
    $result = new BatchTupleResult(100, 4, 3, 1, $this->responses, $this->errors);

    $expected = [
        'totalOperations' => 100,
        'totalChunks' => 4,
        'successfulChunks' => 3,
        'failedChunks' => 1,
        'successRate' => 0.75,
        'isCompleteSuccess' => false,
        'isCompleteFailure' => false,
        'isPartialSuccess' => true,
    ];

    expect($result->jsonSerialize())->toBe($expected);
});

it('serializes complete success case to JSON correctly', function (): void {
    $result = new BatchTupleResult(100, 2, 2, 0);

    $json = $result->jsonSerialize();
    expect($json['isCompleteSuccess'])->toBeTrue();
    expect($json['isCompleteFailure'])->toBeFalse();
    expect($json['isPartialSuccess'])->toBeFalse();
    expect($json['successRate'])->toBe(1.0);
});

it('provides correct schema', function (): void {
    $schema = BatchTupleResult::schema();

    expect($schema)->toBeInstanceOf(SchemaInterface::class);
    expect($schema->getClassName())->toBe(BatchTupleResult::class);

    $properties = $schema->getProperties();
    expect($properties)->toHaveCount(8);

    $propertyNames = array_map(fn ($prop) => $prop->getName(), $properties);
    expect($propertyNames)->toContain('totalOperations');
    expect($propertyNames)->toContain('totalChunks');
    expect($propertyNames)->toContain('successfulChunks');
    expect($propertyNames)->toContain('failedChunks');
    expect($propertyNames)->toContain('successRate');
    expect($propertyNames)->toContain('isCompleteSuccess');
    expect($propertyNames)->toContain('isCompleteFailure');
    expect($propertyNames)->toContain('isPartialSuccess');
});

it('caches schema instance', function (): void {
    $schema1 = BatchTupleResult::schema();
    $schema2 = BatchTupleResult::schema();

    expect($schema1)->toBe($schema2);
});

it('handles empty responses and errors arrays', function (): void {
    $result = new BatchTupleResult(100, 2, 1, 1, [], []);

    expect($result->getResponses())->toBe([]);
    expect($result->getErrors())->toBe([]);
    expect($result->getFirstError())->toBeNull();
});

it('works with default parameters', function (): void {
    $result = new BatchTupleResult(50, 3, 2, 1);

    expect($result->getTotalOperations())->toBe(50);
    expect($result->getTotalChunks())->toBe(3);
    expect($result->getSuccessfulChunks())->toBe(2);
    expect($result->getFailedChunks())->toBe(1);
    expect($result->getResponses())->toBe([]);
    expect($result->getErrors())->toBe([]);
});
