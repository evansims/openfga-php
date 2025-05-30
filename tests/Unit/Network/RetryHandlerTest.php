<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Network;

use OpenFGA\{Exceptions\NetworkException, Network\CircuitBreakerInterface, Network\RetryHandler};
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Message\{RequestInterface, ResponseInterface};
use RuntimeException;
use Throwable;

describe('RetryHandler', function (): void {
    describe('constructor', function (): void {
        test('creates with default max retries', function (): void {
            $circuitBreaker = test()->createMock(CircuitBreakerInterface::class);
            $handler = new RetryHandler($circuitBreaker);

            expect($handler)->toBeInstanceOf(RetryHandler::class);
        });

        test('creates with custom max retries', function (): void {
            $circuitBreaker = test()->createMock(CircuitBreakerInterface::class);
            $handler = new RetryHandler($circuitBreaker, 5);

            expect($handler)->toBeInstanceOf(RetryHandler::class);
        });
    });

    describe('executeWithRetry()', function (): void {
        test('returns response on successful first attempt', function (): void {
            $circuitBreaker = test()->createMock(CircuitBreakerInterface::class);
            $request = test()->createMock(RequestInterface::class);
            $response = test()->createMock(ResponseInterface::class);

            $request->method('getMethod')->willReturn('GET');
            $response->method('getStatusCode')->willReturn(200);
            $response->method('getHeaders')->willReturn([]);

            $circuitBreaker->expects(test()->once())
                ->method('shouldRetry')
                ->with('https://api.example.com')
                ->willReturn(true);
            $circuitBreaker->expects(test()->once())
                ->method('recordSuccess')
                ->with('https://api.example.com');

            $requestExecutor = fn () => $response;

            $handler = new RetryHandler($circuitBreaker);
            $result = $handler->executeWithRetry($requestExecutor, $request, 'https://api.example.com');

            expect($result)->toBe($response);
        });

        test('throws when circuit breaker is open', function (): void {
            $circuitBreaker = test()->createMock(CircuitBreakerInterface::class);
            $request = test()->createMock(RequestInterface::class);
            $response = test()->createMock(ResponseInterface::class);

            $circuitBreaker->expects(test()->once())
                ->method('shouldRetry')
                ->with('https://api.example.com')
                ->willReturn(false);

            $requestExecutor = fn () => $response;

            $handler = new RetryHandler($circuitBreaker);

            expect(fn () => $handler->executeWithRetry($requestExecutor, $request, 'https://api.example.com'))
                ->toThrow(RuntimeException::class);
        });

        test('retries on 429 status code and succeeds', function (): void {
            $circuitBreaker = test()->createMock(CircuitBreakerInterface::class);
            $request = test()->createMock(RequestInterface::class);
            $response = test()->createMock(ResponseInterface::class);
            $failureResponse = test()->createMock(ResponseInterface::class);

            $request->method('getMethod')->willReturn('GET');
            $failureResponse->method('getStatusCode')->willReturn(429);
            $failureResponse->method('getHeaders')->willReturn(['retry-after' => ['0']]);
            $response->method('getStatusCode')->willReturn(200);

            $circuitBreaker->method('shouldRetry')->willReturn(true);
            $circuitBreaker->expects(test()->once())->method('recordSuccess');

            $callCount = 0;
            $requestExecutor = function () use ($failureResponse, $response, &$callCount) {
                ++$callCount;

                return 1 === $callCount ? $failureResponse : $response;
            };

            $handler = new RetryHandler($circuitBreaker);
            $result = $handler->executeWithRetry($requestExecutor, $request, 'https://api.example.com');

            expect($result)->toBe($response);
            expect($callCount)->toBe(2);
        });

        test('retries on 502 status code', function (): void {
            $circuitBreaker = test()->createMock(CircuitBreakerInterface::class);
            $request = test()->createMock(RequestInterface::class);
            $response = test()->createMock(ResponseInterface::class);
            $failureResponse = test()->createMock(ResponseInterface::class);

            $request->method('getMethod')->willReturn('GET');
            $failureResponse->method('getStatusCode')->willReturn(502);
            $failureResponse->method('getHeaders')->willReturn([]);
            $response->method('getStatusCode')->willReturn(200);

            $circuitBreaker->method('shouldRetry')->willReturn(true);
            $circuitBreaker->expects(test()->once())->method('recordSuccess');

            $callCount = 0;
            $requestExecutor = function () use ($failureResponse, $response, &$callCount) {
                ++$callCount;

                return 1 === $callCount ? $failureResponse : $response;
            };

            $handler = new RetryHandler($circuitBreaker);
            $result = $handler->executeWithRetry($requestExecutor, $request, 'https://api.example.com');

            expect($result)->toBe($response);
            expect($callCount)->toBe(2);
        });

        test('retries on 503 status code with maintenance delay', function (): void {
            $circuitBreaker = test()->createMock(CircuitBreakerInterface::class);
            $request = test()->createMock(RequestInterface::class);
            $response = test()->createMock(ResponseInterface::class);
            $failureResponse = test()->createMock(ResponseInterface::class);

            $request->method('getMethod')->willReturn('GET');
            $failureResponse->method('getStatusCode')->willReturn(503);
            $failureResponse->method('getHeaders')->willReturn([]);
            $response->method('getStatusCode')->willReturn(200);

            $circuitBreaker->method('shouldRetry')->willReturn(true);
            $circuitBreaker->expects(test()->once())->method('recordSuccess');

            $callCount = 0;
            $requestExecutor = function () use ($failureResponse, $response, &$callCount) {
                ++$callCount;

                return 1 === $callCount ? $failureResponse : $response;
            };

            $handler = new RetryHandler($circuitBreaker);
            $startTime = microtime(true);
            $result = $handler->executeWithRetry($requestExecutor, $request, 'https://api.example.com');
            $endTime = microtime(true);

            expect($result)->toBe($response);
            expect($callCount)->toBe(2);
            // Should wait at least 4.9 seconds for 503 maintenance delay
            expect($endTime - $startTime)->toBeGreaterThan(4.9);
        });

        test('retries on 504 status code', function (): void {
            $circuitBreaker = test()->createMock(CircuitBreakerInterface::class);
            $request = test()->createMock(RequestInterface::class);
            $response = test()->createMock(ResponseInterface::class);
            $failureResponse = test()->createMock(ResponseInterface::class);

            $request->method('getMethod')->willReturn('GET');
            $failureResponse->method('getStatusCode')->willReturn(504);
            $failureResponse->method('getHeaders')->willReturn([]);
            $response->method('getStatusCode')->willReturn(200);

            $circuitBreaker->method('shouldRetry')->willReturn(true);
            $circuitBreaker->expects(test()->once())->method('recordSuccess');

            $callCount = 0;
            $requestExecutor = function () use ($failureResponse, $response, &$callCount) {
                ++$callCount;

                return 1 === $callCount ? $failureResponse : $response;
            };

            $handler = new RetryHandler($circuitBreaker);
            $result = $handler->executeWithRetry($requestExecutor, $request, 'https://api.example.com');

            expect($result)->toBe($response);
            expect($callCount)->toBe(2);
        });

        test('does not retry on 400 status code', function (): void {
            $circuitBreaker = test()->createMock(CircuitBreakerInterface::class);
            $request = test()->createMock(RequestInterface::class);
            $failureResponse = test()->createMock(ResponseInterface::class);

            $request->method('getMethod')->willReturn('GET');
            $failureResponse->method('getStatusCode')->willReturn(400);

            $circuitBreaker->method('shouldRetry')->willReturn(true);

            $requestExecutor = fn () => $failureResponse;

            $handler = new RetryHandler($circuitBreaker);

            expect(fn () => $handler->executeWithRetry($requestExecutor, $request, 'https://api.example.com'))
                ->toThrow(NetworkException::class);
        });

        test('does not retry on 404 status code', function (): void {
            $circuitBreaker = test()->createMock(CircuitBreakerInterface::class);
            $request = test()->createMock(RequestInterface::class);
            $failureResponse = test()->createMock(ResponseInterface::class);

            $request->method('getMethod')->willReturn('GET');
            $failureResponse->method('getStatusCode')->willReturn(404);

            $circuitBreaker->method('shouldRetry')->willReturn(true);

            $requestExecutor = fn () => $failureResponse;

            $handler = new RetryHandler($circuitBreaker);

            expect(fn () => $handler->executeWithRetry($requestExecutor, $request, 'https://api.example.com'))
                ->toThrow(NetworkException::class);
        });

        test('exhausts retries and throws exception', function (): void {
            $circuitBreaker = test()->createMock(CircuitBreakerInterface::class);
            $request = test()->createMock(RequestInterface::class);
            $failureResponse = test()->createMock(ResponseInterface::class);

            $request->method('getMethod')->willReturn('GET');
            $failureResponse->method('getStatusCode')->willReturn(429);
            $failureResponse->method('getHeaders')->willReturn(['retry-after' => ['0']]);

            $circuitBreaker->method('shouldRetry')->willReturn(true);
            $circuitBreaker->expects(test()->once())->method('recordFailure');

            $requestExecutor = fn () => $failureResponse;

            $handler = new RetryHandler($circuitBreaker, 2);

            expect(fn () => $handler->executeWithRetry($requestExecutor, $request, 'https://api.example.com'))
                ->toThrow(NetworkException::class);
        });

        test('retries on network exception and succeeds', function (): void {
            $circuitBreaker = test()->createMock(CircuitBreakerInterface::class);
            $request = test()->createMock(RequestInterface::class);
            $response = test()->createMock(ResponseInterface::class);
            $networkException = test()->createMock(NetworkExceptionInterface::class);

            $request->method('getMethod')->willReturn('GET');
            $response->method('getStatusCode')->willReturn(200);

            $circuitBreaker->method('shouldRetry')->willReturn(true);
            $circuitBreaker->expects(test()->once())->method('recordSuccess');

            $callCount = 0;
            $requestExecutor = function () use ($networkException, $response, &$callCount) {
                ++$callCount;
                if (1 === $callCount) {
                    throw $networkException;
                }

                return $response;
            };

            $handler = new RetryHandler($circuitBreaker);
            $result = $handler->executeWithRetry($requestExecutor, $request, 'https://api.example.com');

            expect($result)->toBe($response);
            expect($callCount)->toBe(2);
        });

        test('throws network exception after max retries', function (): void {
            $circuitBreaker = test()->createMock(CircuitBreakerInterface::class);
            $request = test()->createMock(RequestInterface::class);
            $networkException = test()->createMock(NetworkExceptionInterface::class);

            $request->method('getMethod')->willReturn('GET');

            $circuitBreaker->method('shouldRetry')->willReturn(true);
            $circuitBreaker->expects(test()->once())->method('recordFailure');

            $requestExecutor = function () use ($networkException): void {
                throw $networkException;
            };

            $handler = new RetryHandler($circuitBreaker, 1);

            try {
                $handler->executeWithRetry($requestExecutor, $request, 'https://api.example.com');
                expect(false)->toBeTrue('Should have thrown an exception');
            } catch (Throwable $e) {
                expect($e)->toBeInstanceOf(NetworkExceptionInterface::class);
            }
        });

        test('does not retry non-network exceptions', function (): void {
            $circuitBreaker = test()->createMock(CircuitBreakerInterface::class);
            $request = test()->createMock(RequestInterface::class);
            $exception = new RuntimeException('Test exception');

            $circuitBreaker->method('shouldRetry')->willReturn(true);
            $circuitBreaker->expects(test()->once())->method('recordFailure');

            $requestExecutor = function () use ($exception): void {
                throw $exception;
            };

            $handler = new RetryHandler($circuitBreaker);

            expect(fn () => $handler->executeWithRetry($requestExecutor, $request, 'https://api.example.com'))
                ->toThrow(RuntimeException::class);
        });

        test('respects retry-after header with seconds', function (): void {
            $circuitBreaker = test()->createMock(CircuitBreakerInterface::class);
            $request = test()->createMock(RequestInterface::class);
            $response = test()->createMock(ResponseInterface::class);
            $failureResponse = test()->createMock(ResponseInterface::class);

            $request->method('getMethod')->willReturn('GET');
            $failureResponse->method('getStatusCode')->willReturn(429);
            $failureResponse->method('getHeaders')->willReturn(['retry-after' => ['2']]);
            $response->method('getStatusCode')->willReturn(200);

            $circuitBreaker->method('shouldRetry')->willReturn(true);
            $circuitBreaker->expects(test()->once())->method('recordSuccess');

            $callCount = 0;
            $requestExecutor = function () use ($failureResponse, $response, &$callCount) {
                ++$callCount;

                return 1 === $callCount ? $failureResponse : $response;
            };

            $handler = new RetryHandler($circuitBreaker);

            $startTime = microtime(true);
            $result = $handler->executeWithRetry($requestExecutor, $request, 'https://api.example.com');
            $endTime = microtime(true);

            expect($result)->toBe($response);
            // Should wait at least 1.9 seconds
            expect($endTime - $startTime)->toBeGreaterThan(1.9);
        });

        test('respects retry-after header with HTTP date', function (): void {
            $circuitBreaker = test()->createMock(CircuitBreakerInterface::class);
            $request = test()->createMock(RequestInterface::class);
            $response = test()->createMock(ResponseInterface::class);
            $failureResponse = test()->createMock(ResponseInterface::class);

            $futureTime = time() + 1;
            $httpDate = gmdate('D, d M Y H:i:s T', $futureTime);

            $request->method('getMethod')->willReturn('GET');
            $failureResponse->method('getStatusCode')->willReturn(429);
            $failureResponse->method('getHeaders')->willReturn(['retry-after' => [$httpDate]]);
            $response->method('getStatusCode')->willReturn(200);

            $circuitBreaker->method('shouldRetry')->willReturn(true);
            $circuitBreaker->expects(test()->once())->method('recordSuccess');

            $callCount = 0;
            $requestExecutor = function () use ($failureResponse, $response, &$callCount) {
                ++$callCount;

                return 1 === $callCount ? $failureResponse : $response;
            };

            $handler = new RetryHandler($circuitBreaker);

            $startTime = microtime(true);
            $result = $handler->executeWithRetry($requestExecutor, $request, 'https://api.example.com');
            $endTime = microtime(true);

            expect($result)->toBe($response);
            // Should wait at least 0.9 seconds
            expect($endTime - $startTime)->toBeGreaterThan(0.9);
        });

        test('respects x-rate-limit-reset header', function (): void {
            $circuitBreaker = test()->createMock(CircuitBreakerInterface::class);
            $request = test()->createMock(RequestInterface::class);
            $response = test()->createMock(ResponseInterface::class);
            $failureResponse = test()->createMock(ResponseInterface::class);

            $resetTime = time() + 1;

            $request->method('getMethod')->willReturn('GET');
            $failureResponse->method('getStatusCode')->willReturn(429);
            $failureResponse->method('getHeaders')->willReturn(['x-rate-limit-reset' => [(string) $resetTime]]);
            $response->method('getStatusCode')->willReturn(200);

            $circuitBreaker->method('shouldRetry')->willReturn(true);
            $circuitBreaker->expects(test()->once())->method('recordSuccess');

            $callCount = 0;
            $requestExecutor = function () use ($failureResponse, $response, &$callCount) {
                ++$callCount;

                return 1 === $callCount ? $failureResponse : $response;
            };

            $handler = new RetryHandler($circuitBreaker);

            $startTime = microtime(true);
            $result = $handler->executeWithRetry($requestExecutor, $request, 'https://api.example.com');
            $endTime = microtime(true);

            expect($result)->toBe($response);
            // Should wait at least 0.9 seconds
            expect($endTime - $startTime)->toBeGreaterThan(0.9);
        });

        test('handles invalid retry-after header gracefully', function (): void {
            $circuitBreaker = test()->createMock(CircuitBreakerInterface::class);
            $request = test()->createMock(RequestInterface::class);
            $response = test()->createMock(ResponseInterface::class);
            $failureResponse = test()->createMock(ResponseInterface::class);

            $request->method('getMethod')->willReturn('GET');
            $failureResponse->method('getStatusCode')->willReturn(429);
            $failureResponse->method('getHeaders')->willReturn(['retry-after' => ['invalid-value']]);
            $response->method('getStatusCode')->willReturn(200);

            $circuitBreaker->method('shouldRetry')->willReturn(true);
            $circuitBreaker->expects(test()->once())->method('recordSuccess');

            $callCount = 0;
            $requestExecutor = function () use ($failureResponse, $response, &$callCount) {
                ++$callCount;

                return 1 === $callCount ? $failureResponse : $response;
            };

            $handler = new RetryHandler($circuitBreaker);
            $result = $handler->executeWithRetry($requestExecutor, $request, 'https://api.example.com');

            expect($result)->toBe($response);
            expect($callCount)->toBe(2);
        });

        test('handles case insensitive headers', function (): void {
            $circuitBreaker = test()->createMock(CircuitBreakerInterface::class);
            $request = test()->createMock(RequestInterface::class);
            $response = test()->createMock(ResponseInterface::class);
            $failureResponse = test()->createMock(ResponseInterface::class);

            $request->method('getMethod')->willReturn('GET');
            $failureResponse->method('getStatusCode')->willReturn(429);
            $failureResponse->method('getHeaders')->willReturn(['Retry-After' => ['1']]);
            $response->method('getStatusCode')->willReturn(200);

            $circuitBreaker->method('shouldRetry')->willReturn(true);
            $circuitBreaker->expects(test()->once())->method('recordSuccess');

            $callCount = 0;
            $requestExecutor = function () use ($failureResponse, $response, &$callCount) {
                ++$callCount;

                return 1 === $callCount ? $failureResponse : $response;
            };

            $handler = new RetryHandler($circuitBreaker);

            $startTime = microtime(true);
            $result = $handler->executeWithRetry($requestExecutor, $request, 'https://api.example.com');
            $endTime = microtime(true);

            expect($result)->toBe($response);
            expect($endTime - $startTime)->toBeGreaterThan(0.9);
        });

        test('uses fast retry for first network error', function (): void {
            $circuitBreaker = test()->createMock(CircuitBreakerInterface::class);
            $request = test()->createMock(RequestInterface::class);
            $response = test()->createMock(ResponseInterface::class);
            $networkException = test()->createMock(NetworkExceptionInterface::class);

            $request->method('getMethod')->willReturn('GET');
            $response->method('getStatusCode')->willReturn(200);

            $circuitBreaker->method('shouldRetry')->willReturn(true);
            $circuitBreaker->expects(test()->once())->method('recordSuccess');

            $callCount = 0;
            $requestExecutor = function () use ($networkException, $response, &$callCount) {
                ++$callCount;
                if (1 === $callCount) {
                    throw $networkException;
                }

                return $response;
            };

            $handler = new RetryHandler($circuitBreaker);

            $startTime = microtime(true);
            $result = $handler->executeWithRetry($requestExecutor, $request, 'https://api.example.com');
            $endTime = microtime(true);

            expect($result)->toBe($response);
            // Should use fast retry (50ms), so total time should be low
            expect($endTime - $startTime)->toBeLessThan(0.2);
            expect($endTime - $startTime)->toBeGreaterThan(0.04);
        });

        test('handles non-idempotent methods conservatively for network errors', function (): void {
            $circuitBreaker = test()->createMock(CircuitBreakerInterface::class);
            $request = test()->createMock(RequestInterface::class);
            $response = test()->createMock(ResponseInterface::class);
            $networkException = test()->createMock(NetworkExceptionInterface::class);

            $request->method('getMethod')->willReturn('POST');
            $response->method('getStatusCode')->willReturn(200);

            $circuitBreaker->method('shouldRetry')->willReturn(true);
            $circuitBreaker->expects(test()->once())->method('recordSuccess');

            $callCount = 0;
            $requestExecutor = function () use ($networkException, $response, &$callCount) {
                ++$callCount;
                if (1 === $callCount) {
                    throw $networkException;
                }

                return $response;
            };

            $handler = new RetryHandler($circuitBreaker);
            $result = $handler->executeWithRetry($requestExecutor, $request, 'https://api.example.com');

            expect($result)->toBe($response);
            expect($callCount)->toBe(2);
        });

        test('does not retry non-idempotent methods after first network error', function (): void {
            $circuitBreaker = test()->createMock(CircuitBreakerInterface::class);
            $request = test()->createMock(RequestInterface::class);
            $networkException = test()->createMock(NetworkExceptionInterface::class);

            $request->method('getMethod')->willReturn('POST');

            $circuitBreaker->method('shouldRetry')->willReturn(true);
            $circuitBreaker->expects(test()->once())->method('recordFailure');

            $callCount = 0;
            $requestExecutor = function () use ($networkException, &$callCount) {
                ++$callCount;
                if (1 === $callCount) {
                    // Simulate first attempt succeeding to test second failure
                    $response = test()->createMock(ResponseInterface::class);
                    $response->method('getStatusCode')->willReturn(429);
                    $response->method('getHeaders')->willReturn(['retry-after' => ['0']]);

                    return $response;
                }

                throw $networkException;
            };

            $handler = new RetryHandler($circuitBreaker, 3);

            try {
                $handler->executeWithRetry($requestExecutor, $request, 'https://api.example.com');
                expect(false)->toBeTrue('Should have thrown an exception');
            } catch (Throwable $e) {
                expect($e)->toBeInstanceOf(NetworkExceptionInterface::class);
            }
        });

        test('retries 429 status for non-idempotent methods', function (): void {
            $circuitBreaker = test()->createMock(CircuitBreakerInterface::class);
            $request = test()->createMock(RequestInterface::class);
            $response = test()->createMock(ResponseInterface::class);
            $failureResponse = test()->createMock(ResponseInterface::class);

            $request->method('getMethod')->willReturn('POST');
            $failureResponse->method('getStatusCode')->willReturn(429);
            $failureResponse->method('getHeaders')->willReturn(['retry-after' => ['0']]);
            $response->method('getStatusCode')->willReturn(200);

            $circuitBreaker->method('shouldRetry')->willReturn(true);
            $circuitBreaker->expects(test()->once())->method('recordSuccess');

            $callCount = 0;
            $requestExecutor = function () use ($failureResponse, $response, &$callCount) {
                ++$callCount;

                return 1 === $callCount ? $failureResponse : $response;
            };

            $handler = new RetryHandler($circuitBreaker);
            $result = $handler->executeWithRetry($requestExecutor, $request, 'https://api.example.com');

            expect($result)->toBe($response);
            expect($callCount)->toBe(2);
        });

        test('does not retry 502 status for non-idempotent methods', function (): void {
            $circuitBreaker = test()->createMock(CircuitBreakerInterface::class);
            $request = test()->createMock(RequestInterface::class);
            $failureResponse = test()->createMock(ResponseInterface::class);

            $request->method('getMethod')->willReturn('POST');
            $failureResponse->method('getStatusCode')->willReturn(502);

            $circuitBreaker->method('shouldRetry')->willReturn(true);

            $requestExecutor = fn () => $failureResponse;

            $handler = new RetryHandler($circuitBreaker);

            expect(fn () => $handler->executeWithRetry($requestExecutor, $request, 'https://api.example.com'))
                ->toThrow(NetworkException::class);
        });

        test('calculates exponential backoff with jitter', function (): void {
            $circuitBreaker = test()->createMock(CircuitBreakerInterface::class);
            $request = test()->createMock(RequestInterface::class);
            $response = test()->createMock(ResponseInterface::class);
            $failureResponse = test()->createMock(ResponseInterface::class);

            $request->method('getMethod')->willReturn('GET');
            $failureResponse->method('getStatusCode')->willReturn(502);
            $failureResponse->method('getHeaders')->willReturn([]);
            $response->method('getStatusCode')->willReturn(200);

            $circuitBreaker->method('shouldRetry')->willReturn(true);
            $circuitBreaker->expects(test()->once())->method('recordSuccess');

            $callCount = 0;
            $delays = [];
            $requestExecutor = function () use ($failureResponse, $response, &$callCount, &$delays) {
                ++$callCount;
                if (1 === $callCount) {
                    $start = microtime(true);

                    return $failureResponse;
                }
                if (2 === $callCount) {
                    $delays[] = microtime(true);

                    return $failureResponse;
                }

                return $response;
            };

            $handler = new RetryHandler($circuitBreaker, 3);

            $startTime = microtime(true);
            $result = $handler->executeWithRetry($requestExecutor, $request, 'https://api.example.com');

            expect($result)->toBe($response);
            expect($callCount)->toBe(3);

            // Should have some delay for exponential backoff
            $totalTime = microtime(true) - $startTime;
            expect($totalTime)->toBeGreaterThan(0.1); // At least 100ms total
        });
    });
});
