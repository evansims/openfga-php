<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Network;

use Exception;
use Fiber;
use OpenFGA\Network\FiberConcurrentExecutor;
use RuntimeException;

describe('FiberConcurrentExecutor', function (): void {
    beforeEach(function (): void {
        $this->executor = new FiberConcurrentExecutor;
    });

    describe('executeParallel()', function (): void {
        test('executes single task', function (): void {
            $tasks = [
                fn () => 'result1',
            ];

            $results = $this->executor->executeParallel($tasks);

            expect($results)->toBe(['result1']);
        });

        test('executes multiple tasks concurrently', function (): void {
            $executionOrder = [];
            $tasks = [
                function () use (&$executionOrder) {
                    $executionOrder[] = 'start1';
                    Fiber::suspend();
                    $executionOrder[] = 'end1';

                    return 'result1';
                },
                function () use (&$executionOrder) {
                    $executionOrder[] = 'start2';
                    Fiber::suspend();
                    $executionOrder[] = 'end2';

                    return 'result2';
                },
                function () use (&$executionOrder) {
                    $executionOrder[] = 'start3';
                    Fiber::suspend();
                    $executionOrder[] = 'end3';

                    return 'result3';
                },
            ];

            $results = $this->executor->executeParallel($tasks, 3);

            expect($results)->toBe(['result1', 'result2', 'result3']);

            // Verify concurrent execution by checking interleaved execution
            expect($executionOrder)->not->toBe(['start1', 'end1', 'start2', 'end2', 'start3', 'end3']);
            expect($executionOrder)->toContain('start1', 'start2', 'start3');
        });

        test('respects concurrency limit', function (): void {
            $activeTasks = 0;
            $maxActiveTasks = 0;

            $tasks = array_fill(0, 10, function () use (&$activeTasks, &$maxActiveTasks) {
                $activeTasks++;
                $maxActiveTasks = max($maxActiveTasks, $activeTasks);

                // Simulate work
                Fiber::suspend();

                $activeTasks--;

                return 'done';
            });

            $results = $this->executor->executeParallel($tasks, 3);

            expect($results)->toHaveCount(10);
            expect($maxActiveTasks)->toBeLessThanOrEqual(3);
        });

        test('preserves task order in results', function (): void {
            $tasks = [
                2 => fn () => 'result2',
                0 => fn () => 'result0',
                1 => fn () => 'result1',
            ];

            $results = $this->executor->executeParallel($tasks);

            expect($results)->toBe([
                2 => 'result2',
                0 => 'result0',
                1 => 'result1',
            ]);
        });

        test('handles task exceptions', function (): void {
            $tasks = [
                fn () => 'result1',
                fn () => throw new RuntimeException('Task error'),
                fn () => 'result3',
            ];

            $results = $this->executor->executeParallel($tasks);

            expect($results[0])->toBe('result1');
            expect($results[1])->toBeInstanceOf(RuntimeException::class);
            expect($results[1]->getMessage())->toBe('Task error');
            expect($results[2])->toBe('result3');
        });

        test('handles empty task array', function (): void {
            $results = $this->executor->executeParallel([]);

            expect($results)->toBe([]);
        });
    });

    describe('supportsConcurrency()', function (): void {
        test('returns true when Fibers are available', function (): void {
            $result = $this->executor->supportsConcurrency();

            expect($result)->toBe(class_exists(Fiber::class));
        });
    });

    describe('getMaxRecommendedConcurrency()', function (): void {
        test('returns reasonable concurrency limit', function (): void {
            $limit = $this->executor->getMaxRecommendedConcurrency();

            expect($limit)->toBeGreaterThan(0);
            expect($limit)->toBeLessThanOrEqual(100); // Reasonable upper bound
        });
    });

    describe('error handling', function (): void {
        test('continues execution when some tasks fail', function (): void {
            $tasks = [
                fn () => 'success1',
                fn () => throw new Exception('error1'),
                fn () => 'success2',
                fn () => throw new Exception('error2'),
                fn () => 'success3',
            ];

            $results = $this->executor->executeParallel($tasks, 2);

            expect($results[0])->toBe('success1');
            expect($results[1])->toBeInstanceOf(Exception::class);
            expect($results[2])->toBe('success2');
            expect($results[3])->toBeInstanceOf(Exception::class);
            expect($results[4])->toBe('success3');
        });

        test('handles exceptions in fiber creation', function (): void {
            $tasks = [
                function (): void {
                    // This task will throw immediately
                    throw new RuntimeException('Immediate error');
                },
                fn () => 'success',
            ];

            $results = $this->executor->executeParallel($tasks);

            expect($results[0])->toBeInstanceOf(RuntimeException::class);
            expect($results[1])->toBe('success');
        });
    });
});
