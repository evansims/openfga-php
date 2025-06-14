<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Responses;

use Exception;
use OpenFGA\Responses\WriteTuplesResponse;
use RuntimeException;

describe('WriteTuplesResponse - Additional Coverage', function (): void {
    describe('constructor and basic methods', function (): void {
        test('creates response with default transactional behavior', function (): void {
            $response = new WriteTuplesResponse;

            expect($response->isTransactional())->toBeTrue();
            expect($response->getTotalOperations())->toBe(0);
            expect($response->getTotalChunks())->toBe(1);
            expect($response->getSuccessfulChunks())->toBe(1);
            expect($response->getFailedChunks())->toBe(0);
            expect($response->getErrors())->toBe([]);
        });

        test('creates response with custom parameters', function (): void {
            $errors = [new Exception('Test error')];
            $response = new WriteTuplesResponse(
                transactional: false,
                totalOperations: 10,
                totalChunks: 5,
                successfulChunks: 3,
                failedChunks: 2,
                errors: $errors,
            );

            expect($response->isTransactional())->toBeFalse();
            expect($response->getTotalOperations())->toBe(10);
            expect($response->getTotalChunks())->toBe(5);
            expect($response->getSuccessfulChunks())->toBe(3);
            expect($response->getFailedChunks())->toBe(2);
            expect($response->getErrors())->toBe($errors);
        });

        test('calculates success rate correctly', function (): void {
            $response = new WriteTuplesResponse(
                totalChunks: 4,
                successfulChunks: 3,
                failedChunks: 1,
            );

            expect($response->getSuccessRate())->toBe(0.75); // 3/4 = 0.75
        });

        test('handles zero total chunks in success rate calculation', function (): void {
            $response = new WriteTuplesResponse(
                totalChunks: 0,
                successfulChunks: 0,
                failedChunks: 0,
            );

            expect($response->getSuccessRate())->toBe(0.0);
        });

        test('identifies complete success correctly', function (): void {
            $response = new WriteTuplesResponse(
                totalChunks: 3,
                successfulChunks: 3,
                failedChunks: 0,
            );

            expect($response->isCompleteSuccess())->toBeTrue();
            expect($response->isCompleteFailure())->toBeFalse();
            expect($response->isPartialSuccess())->toBeFalse();
        });

        test('identifies complete failure correctly', function (): void {
            $response = new WriteTuplesResponse(
                totalChunks: 3,
                successfulChunks: 0,
                failedChunks: 3,
            );

            expect($response->isCompleteSuccess())->toBeFalse();
            expect($response->isCompleteFailure())->toBeTrue();
            expect($response->isPartialSuccess())->toBeFalse();
        });

        test('identifies partial success correctly', function (): void {
            $response = new WriteTuplesResponse(
                totalChunks: 4,
                successfulChunks: 2,
                failedChunks: 2,
            );

            expect($response->isCompleteSuccess())->toBeFalse();
            expect($response->isCompleteFailure())->toBeFalse();
            expect($response->isPartialSuccess())->toBeTrue();
        });

        test('getFirstError returns first error when errors exist', function (): void {
            $error1 = new Exception('First error');
            $error2 = new Exception('Second error');
            $errors = [$error1, $error2];

            $response = new WriteTuplesResponse(
                failedChunks: 2,
                errors: $errors,
            );

            expect($response->getFirstError())->toBe($error1);
        });

        test('getFirstError returns null when no errors exist', function (): void {
            $response = new WriteTuplesResponse;

            expect($response->getFirstError())->toBeNull();
        });

        test('throwOnFailure does nothing when no failures', function (): void {
            $response = new WriteTuplesResponse(
                totalChunks: 2,
                successfulChunks: 2,
                failedChunks: 0,
            );

            // Should not throw
            $response->throwOnFailure();
            expect(true)->toBeTrue(); // Test passes if no exception is thrown
        });

        test('throwOnFailure throws first error when failures exist', function (): void {
            $error = new Exception('Test error');
            $response = new WriteTuplesResponse(
                failedChunks: 1,
                errors: [$error],
            );

            expect(fn () => $response->throwOnFailure())->toThrow(Exception::class, 'Test error');
        });

        test('throwOnFailure throws RuntimeException when no specific error available', function (): void {
            $response = new WriteTuplesResponse(
                totalChunks: 3,
                failedChunks: 1,
            );

            expect(fn () => $response->throwOnFailure())->toThrow(RuntimeException::class);
        });

        test('handles edge case with zero chunks but failures', function (): void {
            $response = new WriteTuplesResponse(
                totalChunks: 0,
                successfulChunks: 0,
                failedChunks: 1, // This is an edge case that shouldn't normally happen
            );

            expect($response->isCompleteFailure())->toBeFalse(); // 0 < totalChunks is false
            expect($response->isCompleteSuccess())->toBeFalse(); // 0 < totalChunks is false
            expect($response->isPartialSuccess())->toBeFalse(); // Both need to be > 0
        });

        test('handles large numbers correctly', function (): void {
            $response = new WriteTuplesResponse(
                totalOperations: 1000000,
                totalChunks: 10000,
                successfulChunks: 7500,
                failedChunks: 2500,
            );

            expect($response->getTotalOperations())->toBe(1000000);
            expect($response->getTotalChunks())->toBe(10000);
            expect($response->getSuccessRate())->toBe(0.75);
            expect($response->isPartialSuccess())->toBeTrue();
        });
    });
});
