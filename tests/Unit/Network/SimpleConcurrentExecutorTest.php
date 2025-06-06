<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Network;

use OpenFGA\Network\SimpleConcurrentExecutor;
use RuntimeException;

describe('SimpleConcurrentExecutor', function (): void {
    beforeEach(function (): void {
        $this->executor = new SimpleConcurrentExecutor;
    });

    describe('executeParallel()', function (): void {
        test('executes tasks sequentially', function (): void {
            $executionOrder = [];
            $tasks = [
                function () use (&$executionOrder) {
                    $executionOrder[] = 'task1';

                    return 'result1';
                },
                function () use (&$executionOrder) {
                    $executionOrder[] = 'task2';

                    return 'result2';
                },
                function () use (&$executionOrder) {
                    $executionOrder[] = 'task3';

                    return 'result3';
                },
            ];

            $results = $this->executor->executeParallel($tasks);

            expect($results)->toBe(['result1', 'result2', 'result3']);
            expect($executionOrder)->toBe(['task1', 'task2', 'task3']);
        });

        test('handles exceptions gracefully', function (): void {
            $tasks = [
                fn () => 'result1',
                fn () => throw new RuntimeException('Error in task 2'),
                fn () => 'result3',
            ];

            $results = $this->executor->executeParallel($tasks);

            expect($results[0])->toBe('result1');
            expect($results[1])->toBeInstanceOf(RuntimeException::class);
            expect($results[1]->getMessage())->toBe('Error in task 2');
            expect($results[2])->toBe('result3');
        });

        test('preserves array keys', function (): void {
            $tasks = [
                'first' => fn () => 'result1',
                'second' => fn () => 'result2',
                'third' => fn () => 'result3',
            ];

            $results = $this->executor->executeParallel($tasks);

            expect($results)->toBe([
                'first' => 'result1',
                'second' => 'result2',
                'third' => 'result3',
            ]);
        });

        test('handles empty task array', function (): void {
            $results = $this->executor->executeParallel([]);

            expect($results)->toBe([]);
        });
    });

    describe('supportsConcurrency()', function (): void {
        test('returns false', function (): void {
            expect($this->executor->supportsConcurrency())->toBeFalse();
        });
    });

    describe('getMaxRecommendedConcurrency()', function (): void {
        test('returns 1 for sequential execution', function (): void {
            expect($this->executor->getMaxRecommendedConcurrency())->toBe(1);
        });
    });
});
