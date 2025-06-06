<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Network;

use Exception;
use OpenFGA\Exceptions\{NetworkError, NetworkException};
use OpenFGA\Network\ExponentialBackoffRetryStrategy;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Message\{RequestInterface, ResponseInterface};
use RuntimeException;

describe('ExponentialBackoffRetryStrategy', function (): void {
    beforeEach(function (): void {
        $this->strategy = new ExponentialBackoffRetryStrategy(
            maxRetries: 3,
            initialDelayMs: 100,
            maxDelayMs: 1000,
            backoffFactor: 2.0,
            jitterFactor: 0.0, // Disable jitter for predictable tests
        );
    });

    describe('execute()', function (): void {
        test('returns result on successful operation', function (): void {
            $operation = fn () => 'success';

            $result = $this->strategy->execute($operation);

            expect($result)->toBe('success');
        });

        test('retries on retryable exception', function (): void {
            $attempts = 0;
            $operation = function () use (&$attempts) {
                $attempts++;

                if (3 > $attempts) {
                    throw NetworkError::Request->exception();
                }

                return 'success after retries';
            };

            $result = $this->strategy->execute($operation);

            expect($result)->toBe('success after retries');
            expect($attempts)->toBe(3);
        });

        test('throws exception after max retries', function (): void {
            $attempts = 0;
            $operation = function () use (&$attempts): void {
                $attempts++;

                throw NetworkError::Request->exception(context: ['message' => 'Persistent network error']);
            };

            expect(fn () => $this->strategy->execute($operation))
                ->toThrow(NetworkException::class);

            expect($attempts)->toBe(4); // Initial attempt + 3 retries
        });

        test('does not retry non-retryable exceptions', function (): void {
            $attempts = 0;
            $operation = function () use (&$attempts): void {
                $attempts++;

                throw new RuntimeException('Non-retryable error');
            };

            expect(fn () => $this->strategy->execute($operation))
                ->toThrow(RuntimeException::class, 'Non-retryable error');

            expect($attempts)->toBe(1); // Only initial attempt, no retries
        });

        test('respects custom max retries in config', function (): void {
            $attempts = 0;
            $operation = function () use (&$attempts): void {
                $attempts++;

                throw NetworkError::Request->exception();
            };

            expect(fn () => $this->strategy->execute($operation, ['max_retries' => 1]))
                ->toThrow(NetworkException::class);

            expect($attempts)->toBe(2); // Initial attempt + 1 retry
        });
    });

    describe('isRetryable()', function (): void {
        test('identifies network exceptions as retryable', function (): void {
            $exception = NetworkError::Request->exception();

            expect($this->strategy->isRetryable($exception))->toBeTrue();
        });

        test('identifies PSR network exceptions as retryable', function (): void {
            $mockRequest = test()->createMock(RequestInterface::class);
            $exception = new class($mockRequest) extends Exception implements NetworkExceptionInterface {
                public function __construct(private $request)
                {
                    parent::__construct('Network exception');
                }

                public function getRequest(): RequestInterface
                {
                    return $this->request;
                }
            };

            expect($this->strategy->isRetryable($exception))->toBeTrue();
        });

        test('identifies specific HTTP status codes as retryable', function (): void {
            $mockResponse = test()->createMock(ResponseInterface::class);

            // Test retryable status codes
            $retryableCodes = [408, 429, 502, 503, 504];

            foreach ($retryableCodes as $code) {
                $mockResponse->method('getStatusCode')->willReturn($code);
                $exception = NetworkError::Request->exception(response: $mockResponse);

                expect($this->strategy->isRetryable($exception))->toBeTrue();
            }
        });

        test('identifies non-retryable status codes', function (): void {
            $mockResponse = test()->createMock(ResponseInterface::class);
            $mockResponse->method('getStatusCode')->willReturn(404);

            $exception = NetworkError::UndefinedEndpoint->exception(response: $mockResponse);

            expect($this->strategy->isRetryable($exception))->toBeFalse();
        });

        test('identifies generic exceptions as non-retryable', function (): void {
            $exception = new RuntimeException('Generic error');

            expect($this->strategy->isRetryable($exception))->toBeFalse();
        });
    });

    describe('getRetryDelay()', function (): void {
        test('calculates exponential backoff delays', function (): void {
            // With jitter disabled, delays should be predictable
            expect($this->strategy->getRetryDelay(1))->toBe(100);  // 100 * 2^0
            expect($this->strategy->getRetryDelay(2))->toBe(200);  // 100 * 2^1
            expect($this->strategy->getRetryDelay(3))->toBe(400);  // 100 * 2^2
            expect($this->strategy->getRetryDelay(4))->toBe(800);  // 100 * 2^3
        });

        test('caps delay at maximum', function (): void {
            expect($this->strategy->getRetryDelay(10))->toBe(1000); // Capped at maxDelayMs
        });

        test('applies jitter when enabled', function (): void {
            $strategyWithJitter = new ExponentialBackoffRetryStrategy(
                maxRetries: 3,
                initialDelayMs: 100,
                maxDelayMs: 1000,
                backoffFactor: 2.0,
                jitterFactor: 0.5,
            );

            // With 50% jitter, delay should be within +/- 50% of base delay
            $baseDelay = 200; // For attempt 2
            $delay = $strategyWithJitter->getRetryDelay(2);

            expect($delay)->toBeGreaterThanOrEqual(100); // 200 - 100
            expect($delay)->toBeLessThanOrEqual(300);    // 200 + 100
        });

        test('uses custom config values', function (): void {
            $config = [
                'initial_delay_ms' => 50,
                'max_delay_ms' => 500,
                'backoff_factor' => 3.0,
            ];

            expect($this->strategy->getRetryDelay(1, $config))->toBe(50);   // 50 * 3^0
            expect($this->strategy->getRetryDelay(2, $config))->toBe(150);  // 50 * 3^1
            expect($this->strategy->getRetryDelay(3, $config))->toBe(450);  // 50 * 3^2
            expect($this->strategy->getRetryDelay(4, $config))->toBe(500);  // Capped at 500
        });
    });
});
