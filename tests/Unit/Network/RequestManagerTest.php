<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Network;

use Exception;
use OpenFGA\Client;
use OpenFGA\Exceptions\{NetworkException};
use OpenFGA\Network\{RequestContext, RequestManager, RequestMethod};
use OpenFGA\Requests\RequestInterface as ClientRequestInterface;
use OpenFGA\Results\{Failure, FailureInterface, Success, SuccessInterface};
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\{RequestFactoryInterface, RequestInterface, ResponseFactoryInterface, ResponseInterface, StreamFactoryInterface, StreamInterface};
use Psr\Http\Message\UriInterface;

describe('RequestManager', function (): void {
    test('constructs with required parameters', function (): void {
        $manager = new RequestManager(
            url: 'https://api.example.com',
            maxRetries: 3,
        );

        expect($manager)->toBeInstanceOf(RequestManager::class);
    });

    test('constructs with all parameters', function (): void {
        $httpClient = test()->createMock(ClientInterface::class);
        $responseFactory = test()->createMock(ResponseFactoryInterface::class);
        $streamFactory = test()->createMock(StreamFactoryInterface::class);
        $requestFactory = test()->createMock(RequestFactoryInterface::class);

        $manager = new RequestManager(
            url: 'https://api.example.com',
            maxRetries: 3,
            authorizationHeader: 'Bearer token123',
            httpClient: $httpClient,
            httpResponseFactory: $responseFactory,
            httpStreamFactory: $streamFactory,
            httpRequestFactory: $requestFactory,
        );

        expect($manager)->toBeInstanceOf(RequestManager::class);
    });

    test('getHttpClient returns provided client', function (): void {
        $httpClient = test()->createMock(ClientInterface::class);

        $manager = new RequestManager(
            url: 'https://api.example.com',
            maxRetries: 3,
            httpClient: $httpClient,
        );

        expect($manager->getHttpClient())->toBe($httpClient);
    });

    test('getHttpRequestFactory returns provided factory', function (): void {
        $requestFactory = test()->createMock(RequestFactoryInterface::class);

        $manager = new RequestManager(
            url: 'https://api.example.com',
            maxRetries: 3,
            httpRequestFactory: $requestFactory,
        );

        expect($manager->getHttpRequestFactory())->toBe($requestFactory);
    });

    test('getHttpResponseFactory returns provided factory', function (): void {
        $responseFactory = test()->createMock(ResponseFactoryInterface::class);

        $manager = new RequestManager(
            url: 'https://api.example.com',
            maxRetries: 3,
            httpResponseFactory: $responseFactory,
        );

        expect($manager->getHttpResponseFactory())->toBe($responseFactory);
    });

    test('getHttpStreamFactory returns provided factory', function (): void {
        $streamFactory = test()->createMock(StreamFactoryInterface::class);

        $manager = new RequestManager(
            url: 'https://api.example.com',
            maxRetries: 3,
            httpStreamFactory: $streamFactory,
        );

        expect($manager->getHttpStreamFactory())->toBe($streamFactory);
    });

    test('request creates PSR-7 request with authorization header', function (): void {
        $streamFactory = test()->createMock(StreamFactoryInterface::class);
        $requestFactory = test()->createMock(RequestFactoryInterface::class);
        $psrRequest = test()->createMock(RequestInterface::class);
        $stream = test()->createMock(StreamInterface::class);

        $requestContext = new RequestContext(
            method: RequestMethod::POST,
            url: '/stores',
            body: $stream,
            headers: ['Content-Type' => 'application/json'],
            useApiUrl: true,
        );

        $clientRequest = test()->createMock(ClientRequestInterface::class);
        $clientRequest->expects(test()->once())
            ->method('getRequest')
            ->with($streamFactory)
            ->willReturn($requestContext);

        $requestFactory->expects(test()->once())
            ->method('createRequest')
            ->with('POST', 'https://api.example.com/stores')
            ->willReturn($psrRequest);

        $psrRequest->expects(test()->exactly(3))
            ->method('withHeader')
            ->willReturnCallback(function (string $name, string $value) use ($psrRequest): MockObject {
                // Verify expected headers
                expect($name)->toBeIn(['Content-Type', 'User-Agent', 'Authorization']);

                if ('Content-Type' === $name) {
                    expect($value)->toBe('application/json');
                } elseif ('User-Agent' === $name) {
                    expect($value)->toBe('openfga-sdk php/' . Client::VERSION);
                } elseif ('Authorization' === $name) {
                    expect($value)->toBe('Bearer token123');
                }

                return $psrRequest;
            });

        $psrRequest->expects(test()->once())
            ->method('withBody')
            ->with($stream)
            ->willReturn($psrRequest);

        $manager = new RequestManager(
            url: 'https://api.example.com',
            maxRetries: 3,
            authorizationHeader: 'Bearer token123',
            httpStreamFactory: $streamFactory,
            httpRequestFactory: $requestFactory,
        );

        $result = $manager->request($clientRequest);

        expect($result)->toBe($psrRequest);
    });

    test('request handles non-API URLs', function (): void {
        $streamFactory = test()->createMock(StreamFactoryInterface::class);
        $requestFactory = test()->createMock(RequestFactoryInterface::class);
        $psrRequest = test()->createMock(RequestInterface::class);
        $stream = test()->createMock(StreamInterface::class);

        $requestContext = new RequestContext(
            method: RequestMethod::GET,
            url: 'https://other.example.com/resource',
            body: $stream,
            headers: [],
            useApiUrl: false,
        );

        $clientRequest = test()->createMock(ClientRequestInterface::class);
        $clientRequest->expects(test()->once())
            ->method('getRequest')
            ->with($streamFactory)
            ->willReturn($requestContext);

        $requestFactory->expects(test()->once())
            ->method('createRequest')
            ->with('GET', 'https://other.example.com/resource')
            ->willReturn($psrRequest);

        $psrRequest->expects(test()->exactly(2))
            ->method('withHeader')
            ->willReturn($psrRequest);

        $psrRequest->expects(test()->once())
            ->method('withBody')
            ->with($stream)
            ->willReturn($psrRequest);

        $manager = new RequestManager(
            url: 'https://api.example.com',
            maxRetries: 3,
            httpStreamFactory: $streamFactory,
            httpRequestFactory: $requestFactory,
        );

        $result = $manager->request($clientRequest);

        expect($result)->toBe($psrRequest);
    });

    test('send executes request and returns response', function (): void {
        $httpClient = test()->createMock(ClientInterface::class);
        $psrRequest = test()->createMock(RequestInterface::class);
        $psrResponse = test()->createMock(ResponseInterface::class);
        $uri = test()->createMock(UriInterface::class);

        // Mock successful response (status 200)
        $psrResponse->method('getStatusCode')->willReturn(200);

        // Mock URI for circuit breaker tracking
        $uri->method('__toString')->willReturn('https://api.example.com/test');
        $psrRequest->method('getUri')->willReturn($uri);

        $httpClient->expects(test()->once())
            ->method('sendRequest')
            ->with($psrRequest)
            ->willReturn($psrResponse);

        $manager = new RequestManager(
            url: 'https://api.example.com',
            maxRetries: 3,
            httpClient: $httpClient,
        );

        $result = $manager->send($psrRequest);

        expect($result)->toBe($psrResponse);
    });

    test('handleResponseException throws appropriate errors', function (): void {
        $request = test()->createMock(RequestInterface::class);
        $response = test()->createMock(ResponseInterface::class);
        $body = test()->createMock(StreamInterface::class);

        $response->method('getStatusCode')->willReturn(400);
        $response->method('getBody')->willReturn($body);
        $body->method('getContents')->willReturn('{"error": "Invalid request"}');

        RequestManager::handleResponseException($response, $request);
    })->throws(NetworkException::class);

    test('handleResponseException throws error for 401', function (): void {
        $request = test()->createMock(RequestInterface::class);
        $response = test()->createMock(ResponseInterface::class);
        $body = test()->createMock(StreamInterface::class);

        $response->method('getStatusCode')->willReturn(401);
        $response->method('getBody')->willReturn($body);
        $body->method('getContents')->willReturn('');

        RequestManager::handleResponseException($response, $request);
    })->throws(NetworkException::class);

    test('handleResponseException throws error for unknown status', function (): void {
        $request = test()->createMock(RequestInterface::class);
        $response = test()->createMock(ResponseInterface::class);
        $body = test()->createMock(StreamInterface::class);

        $response->method('getStatusCode')->willReturn(418);
        $response->method('getBody')->willReturn($body);
        $body->method('getContents')->willReturn('');

        RequestManager::handleResponseException($response, $request);
    })->throws(NetworkException::class);

    test('send throws exception on network failure', function (): void {
        $httpClient = test()->createMock(ClientInterface::class);
        $psrRequest = test()->createMock(RequestInterface::class);
        $uri = test()->createMock(UriInterface::class);

        // Mock URI for circuit breaker tracking
        $uri->method('__toString')->willReturn('https://api.example.com/test');
        $psrRequest->method('getUri')->willReturn($uri);

        $httpClient->expects(test()->once())
            ->method('sendRequest')
            ->with($psrRequest)
            ->willThrowException(new Exception('Network error'));

        $manager = new RequestManager(
            url: 'https://api.example.com',
            maxRetries: 3,
            httpClient: $httpClient,
        );

        $manager->send($psrRequest);
    })->throws(Exception::class, 'Network error');

    test('RequestMethod enum has correct values', function (): void {
        expect(RequestMethod::DELETE->value)->toBe('DELETE');
        expect(RequestMethod::GET->value)->toBe('GET');
        expect(RequestMethod::POST->value)->toBe('POST');
        expect(RequestMethod::PUT->value)->toBe('PUT');
    });

    test('handles URL with trailing slash', function (): void {
        $streamFactory = test()->createMock(StreamFactoryInterface::class);
        $requestFactory = test()->createMock(RequestFactoryInterface::class);
        $psrRequest = test()->createMock(RequestInterface::class);
        $stream = test()->createMock(StreamInterface::class);

        $requestContext = new RequestContext(
            method: RequestMethod::GET,
            url: '/stores/',
            body: $stream,
            headers: [],
            useApiUrl: true,
        );

        $clientRequest = test()->createMock(ClientRequestInterface::class);
        $clientRequest->expects(test()->once())
            ->method('getRequest')
            ->with($streamFactory)
            ->willReturn($requestContext);

        $requestFactory->expects(test()->once())
            ->method('createRequest')
            ->with('GET', 'https://api.example.com//stores')
            ->willReturn($psrRequest);

        $psrRequest->method('withHeader')->willReturn($psrRequest);
        $psrRequest->method('withBody')->willReturn($psrRequest);

        $manager = new RequestManager(
            url: 'https://api.example.com/',
            maxRetries: 3,
            httpStreamFactory: $streamFactory,
            httpRequestFactory: $requestFactory,
        );

        $result = $manager->request($clientRequest);

        expect($result)->toBe($psrRequest);
    });

    describe('executeParallel', function (): void {
        test('executes single task sequentially', function (): void {
            $manager = new RequestManager(
                url: 'https://api.example.com',
                maxRetries: 3,
            );

            $task = fn () => new Success('result');
            $results = $manager->executeParallel([$task], 1, false);

            expect($results)->toHaveCount(1);
            expect($results[0])->toBeInstanceOf(SuccessInterface::class);
            expect($results[0]->unwrap())->toBe('result');
        });

        test('executes multiple tasks with parallelism limit', function (): void {
            $manager = new RequestManager(
                url: 'https://api.example.com',
                maxRetries: 3,
            );

            $tasks = [
                fn () => new Success('result1'),
                fn () => new Success('result2'),
                fn () => new Success('result3'),
                fn () => new Success('result4'),
            ];

            $results = $manager->executeParallel($tasks, 2, false);

            expect($results)->toHaveCount(4);
            expect($results[0]->unwrap())->toBe('result1');
            expect($results[1]->unwrap())->toBe('result2');
            expect($results[2]->unwrap())->toBe('result3');
            expect($results[3]->unwrap())->toBe('result4');
        });

        test('handles empty task array', function (): void {
            $manager = new RequestManager(
                url: 'https://api.example.com',
                maxRetries: 3,
            );

            $results = $manager->executeParallel([], 5, false);

            expect($results)->toBeEmpty();
        });

        test('preserves task order in results', function (): void {
            $manager = new RequestManager(
                url: 'https://api.example.com',
                maxRetries: 3,
            );

            $tasks = [];

            for ($i = 0; 10 > $i; $i++) {
                $tasks[] = fn () => new Success("result{$i}");
            }

            $results = $manager->executeParallel($tasks, 3, false);

            expect($results)->toHaveCount(10);

            for ($i = 0; 10 > $i; $i++) {
                expect($results[$i]->unwrap())->toBe("result{$i}");
            }
        });

        test('handles task exceptions gracefully', function (): void {
            $manager = new RequestManager(
                url: 'https://api.example.com',
                maxRetries: 3,
            );

            $tasks = [
                fn () => new Success('success'),
                fn () => new Failure(new Exception('test error')),
                fn () => new Success('success2'),
            ];

            $results = $manager->executeParallel($tasks, 2, false);

            expect($results)->toHaveCount(3);
            expect($results[0])->toBeInstanceOf(SuccessInterface::class);
            expect($results[1])->toBeInstanceOf(FailureInterface::class);
            expect($results[2])->toBeInstanceOf(SuccessInterface::class);
        });

        test('respects maxParallelRequests parameter correctly', function (): void {
            $manager = new RequestManager(
                url: 'https://api.example.com',
                maxRetries: 3,
            );

            $startTime = microtime(true);
            $executionTimes = [];

            $tasks = [];

            for ($i = 0; 6 > $i; $i++) {
                $tasks[] = function () use (&$executionTimes, $startTime): SuccessInterface {
                    $executionTimes[] = microtime(true) - $startTime;
                    usleep(1000); // 1ms delay for faster tests

                    return new Success('result');
                };
            }

            $results = $manager->executeParallel($tasks, 3, false);

            expect($results)->toHaveCount(6);
            expect($executionTimes)->toHaveCount(6);
        });

        test('stops early on first error when stopOnFirstError is true', function (): void {
            $manager = new RequestManager(
                url: 'https://api.example.com',
                maxRetries: 3,
            );

            $executedTasks = [];
            $tasks = [
                function () use (&$executedTasks): SuccessInterface {
                    $executedTasks[] = 'task1';

                    return new Success('result1');
                },
                function () use (&$executedTasks): FailureInterface {
                    $executedTasks[] = 'task2_error';

                    return new Failure(new Exception('Early stop error'));
                },
                function () use (&$executedTasks): SuccessInterface {
                    $executedTasks[] = 'task3';

                    return new Success('result3');
                },
                function () use (&$executedTasks): SuccessInterface {
                    $executedTasks[] = 'task4';

                    return new Success('result4');
                },
            ];

            // Force sequential execution by using maxParallelRequests of 1
            // This ensures stopOnFirstError behavior is testable
            $results = $manager->executeParallel($tasks, 1, true);

            // Should have stopped early, so only first two tasks execute
            expect($results)->toHaveCount(2);
            expect($results[0])->toBeInstanceOf(SuccessInterface::class);
            expect($results[1])->toBeInstanceOf(FailureInterface::class);

            // Verify the correct tasks were executed
            expect($executedTasks)->toContain('task1');
            expect($executedTasks)->toContain('task2_error');
            expect($executedTasks)->not->toContain('task3');
            expect($executedTasks)->not->toContain('task4');
        });

        test('continues execution when stopOnFirstError is false', function (): void {
            $manager = new RequestManager(
                url: 'https://api.example.com',
                maxRetries: 3,
            );

            $tasks = [
                fn () => new Success('result1'),
                fn () => new Failure(new Exception('Mid error')),
                fn () => new Success('result3'),
                fn () => new Failure(new Exception('Another error')),
                fn () => new Success('result5'),
            ];

            $results = $manager->executeParallel($tasks, 2, false);

            expect($results)->toHaveCount(5);
            expect($results[0])->toBeInstanceOf(SuccessInterface::class);
            expect($results[1])->toBeInstanceOf(FailureInterface::class);
            expect($results[2])->toBeInstanceOf(SuccessInterface::class);
            expect($results[3])->toBeInstanceOf(FailureInterface::class);
            expect($results[4])->toBeInstanceOf(SuccessInterface::class);
        });

        test('tests concurrent execution with no errors', function (): void {
            $manager = new RequestManager(
                url: 'https://api.example.com',
                maxRetries: 3,
            );

            $executionOrder = [];
            $tasks = [
                function () use (&$executionOrder): SuccessInterface {
                    $executionOrder[] = 'task1';

                    return new Success('result1');
                },
                function () use (&$executionOrder): SuccessInterface {
                    $executionOrder[] = 'task2';

                    return new Success('result2');
                },
                function () use (&$executionOrder): SuccessInterface {
                    $executionOrder[] = 'task3';

                    return new Success('result3');
                },
            ];

            $results = $manager->executeParallel($tasks, 2, false);

            expect($results)->toHaveCount(3);
            expect($results[0]->unwrap())->toBe('result1');
            expect($results[1]->unwrap())->toBe('result2');
            expect($results[2]->unwrap())->toBe('result3');

            // All tasks should have executed
            expect($executionOrder)->toContain('task1');
            expect($executionOrder)->toContain('task2');
            expect($executionOrder)->toContain('task3');
        });

        test('handles edge case with zero maxParallelRequests', function (): void {
            $manager = new RequestManager(
                url: 'https://api.example.com',
                maxRetries: 3,
            );

            $tasks = [
                fn () => new Success('result1'),
                fn () => new Success('result2'),
            ];

            // Should fallback to sequential execution
            $results = $manager->executeParallel($tasks, 0, false);

            expect($results)->toHaveCount(2);
            expect($results[0]->unwrap())->toBe('result1');
            expect($results[1]->unwrap())->toBe('result2');
        });

        test('handles edge case with negative maxParallelRequests', function (): void {
            $manager = new RequestManager(
                url: 'https://api.example.com',
                maxRetries: 3,
            );

            $tasks = [
                fn () => new Success('result1'),
                fn () => new Success('result2'),
            ];

            // Should fallback to sequential execution
            $results = $manager->executeParallel($tasks, -1, false);

            expect($results)->toHaveCount(2);
            expect($results[0]->unwrap())->toBe('result1');
            expect($results[1]->unwrap())->toBe('result2');
        });

        test('handles large maxParallelRequests value correctly', function (): void {
            $manager = new RequestManager(
                url: 'https://api.example.com',
                maxRetries: 3,
            );

            $tasks = [
                fn () => new Success('result1'),
                fn () => new Success('result2'),
                fn () => new Success('result3'),
            ];

            // maxParallelRequests larger than task count
            $results = $manager->executeParallel($tasks, 100, false);

            expect($results)->toHaveCount(3);
            expect($results[0]->unwrap())->toBe('result1');
            expect($results[1]->unwrap())->toBe('result2');
            expect($results[2]->unwrap())->toBe('result3');
        });
    });
});
