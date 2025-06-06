<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Services;

use Exception;
use OpenFGA\Models\{BatchCheckItem, TupleKey, UserTypeFilter};
use OpenFGA\Models\Collections\{BatchCheckItems, TupleKeys, UserTypeFilters};
use OpenFGA\Models\Enums\Consistency;
use OpenFGA\Requests\{BatchCheckRequest, CheckRequest, ExpandRequest, ListObjectsRequest, ListUsersRequest};
use OpenFGA\Responses\{BatchCheckResponse, CheckResponse, ExpandResponse, ListObjectsResponse, ListUsersResponse};
use OpenFGA\Results\{Failure, Success};
use OpenFGA\Services\{AuthorizationService, HttpServiceInterface};
use Psr\Http\Message\{RequestInterface as HttpRequestInterface, ResponseInterface as HttpResponseInterface, StreamInterface};

describe('AuthorizationService', function (): void {
    beforeEach(function (): void {
        $this->httpService = test()->createMock(HttpServiceInterface::class);
        $this->service = new AuthorizationService($this->httpService);

        // Create mock responses
        $this->mockResponse = test()->createMock(HttpResponseInterface::class);
        $this->mockRequest = test()->createMock(HttpRequestInterface::class);
        $this->mockStream = test()->createMock(StreamInterface::class);

        // Default response behavior
        $this->mockResponse->method('getStatusCode')->willReturn(200);
        $this->mockResponse->method('getBody')->willReturn($this->mockStream);
        $this->mockStream->method('__toString')->willReturn('{"allowed": true}');
    });

    describe('check', function (): void {
        test('performs a successful authorization check', function (): void {
            $store = 'test-store';
            $model = 'test-model';
            $tupleKey = new TupleKey(
                user: 'user:anne',
                relation: 'reader',
                object: 'document:budget',
            );

            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->with(test()->isInstanceOf(CheckRequest::class))
                ->willReturn($this->mockResponse);

            $this->httpService
                ->expects(test()->once())
                ->method('getLastRequest')
                ->willReturn($this->mockRequest);

            $result = $this->service->check($store, $model, $tupleKey);

            expect($result)->toBeInstanceOf(Success::class);
            expect($result->unwrap())->toBeInstanceOf(CheckResponse::class);
        });

        test('handles authorization check with all optional parameters', function (): void {
            $store = 'test-store';
            $model = 'test-model';
            $tupleKey = new TupleKey(
                user: 'user:anne',
                relation: 'reader',
                object: 'document:budget',
            );
            $contextualTuples = new TupleKeys([
                new TupleKey(
                    user: 'user:anne',
                    relation: 'member',
                    object: 'group:finance',
                ),
            ]);

            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->with(test()->callback(fn ($request) => $request instanceof CheckRequest
                        && true === $request->getTrace()
                        && Consistency::HIGHER_CONSISTENCY === $request->getConsistency()))
                ->willReturn($this->mockResponse);

            $this->httpService
                ->expects(test()->once())
                ->method('getLastRequest')
                ->willReturn($this->mockRequest);

            $result = $this->service->check(
                $store,
                $model,
                $tupleKey,
                trace: true,
                context: (object) ['time' => '2024-01-01'],
                contextualTuples: $contextualTuples,
                consistency: Consistency::HIGHER_CONSISTENCY,
            );

            expect($result)->toBeInstanceOf(Success::class);
        });

        test('returns failure when HTTP request fails', function (): void {
            $tupleKey = new TupleKey(
                user: 'user:anne',
                relation: 'reader',
                object: 'document:budget',
            );

            $exception = new Exception('Network error');
            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->willThrowException($exception);

            $result = $this->service->check('store', 'model', $tupleKey);

            expect($result)->toBeInstanceOf(Failure::class);
            expect(fn () => $result->unwrap())->toThrow(Exception::class);
        });

        test('returns failure when last request is not available', function (): void {
            $tupleKey = new TupleKey(
                user: 'user:anne',
                relation: 'reader',
                object: 'document:budget',
            );

            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->willReturn($this->mockResponse);

            $this->httpService
                ->expects(test()->once())
                ->method('getLastRequest')
                ->willReturn(null);

            $result = $this->service->check('store', 'model', $tupleKey);

            expect($result)->toBeInstanceOf(Failure::class);
        });
    });

    describe('expand', function (): void {
        test('expands a relationship successfully', function (): void {
            $store = 'test-store';
            $tupleKey = new TupleKey(
                user: 'user:anne',
                relation: 'reader',
                object: 'document:budget',
            );

            $this->mockStream->method('__toString')->willReturn('{"tree": {"root": {"type": "leaf"}}}');

            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->with(test()->isInstanceOf(ExpandRequest::class))
                ->willReturn($this->mockResponse);

            $this->httpService
                ->expects(test()->once())
                ->method('getLastRequest')
                ->willReturn($this->mockRequest);

            $result = $this->service->expand($store, $tupleKey);

            expect($result)->toBeInstanceOf(Success::class);
            expect($result->unwrap())->toBeInstanceOf(ExpandResponse::class);
        });

        test('expands with optional model parameter', function (): void {
            $store = 'test-store';
            $model = 'test-model';
            $tupleKey = new TupleKey(
                user: 'user:anne',
                relation: 'reader',
                object: 'document:budget',
            );

            $this->mockStream->method('__toString')->willReturn('{"tree": {"root": {"type": "leaf"}}}');

            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->with(test()->callback(fn ($request) => $request instanceof ExpandRequest
                        && $request->getModel() === $model))
                ->willReturn($this->mockResponse);

            $this->httpService
                ->expects(test()->once())
                ->method('getLastRequest')
                ->willReturn($this->mockRequest);

            $result = $this->service->expand($store, $tupleKey, $model);

            expect($result)->toBeInstanceOf(Success::class);
        });
    });

    describe('listObjects', function (): void {
        test('lists objects with a relationship successfully', function (): void {
            $store = 'test-store';
            $model = 'test-model';

            // Create a fresh mock response for this test
            $mockResponse = test()->createMock(HttpResponseInterface::class);
            $mockStream = test()->createMock(StreamInterface::class);

            $mockResponse->method('getStatusCode')->willReturn(200);
            $mockResponse->method('getBody')->willReturn($mockStream);
            $mockStream->method('__toString')->willReturn('{"objects": ["document:budget", "document:roadmap"]}');

            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->with(test()->isInstanceOf(ListObjectsRequest::class))
                ->willReturn($mockResponse);

            $this->httpService
                ->expects(test()->once())
                ->method('getLastRequest')
                ->willReturn($this->mockRequest);

            $result = $this->service->listObjects(
                $store,
                $model,
                'document',
                'reader',
                'user:anne',
            );

            expect($result)->toBeInstanceOf(Success::class);
            expect($result->unwrap())->toBeInstanceOf(ListObjectsResponse::class);
        });
    });

    describe('listUsers', function (): void {
        test('lists users with a relationship successfully', function (): void {
            $store = 'test-store';
            $model = 'test-model';
            $userFilters = new UserTypeFilters([
                new UserTypeFilter(type: 'user'),
                new UserTypeFilter(type: 'group'),
            ]);

            // Create a fresh mock response for this test
            $mockResponse = test()->createMock(HttpResponseInterface::class);
            $mockStream = test()->createMock(StreamInterface::class);

            $mockResponse->method('getStatusCode')->willReturn(200);
            $mockResponse->method('getBody')->willReturn($mockStream);
            $mockStream->method('__toString')->willReturn('{"users": [{"object": {"type": "user", "id": "anne"}}]}');

            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->with(test()->isInstanceOf(ListUsersRequest::class))
                ->willReturn($mockResponse);

            $this->httpService
                ->expects(test()->once())
                ->method('getLastRequest')
                ->willReturn($this->mockRequest);

            $result = $this->service->listUsers(
                $store,
                $model,
                'document:budget',
                'reader',
                $userFilters,
            );

            expect($result)->toBeInstanceOf(Success::class);
            expect($result->unwrap())->toBeInstanceOf(ListUsersResponse::class);
        });
    });

    describe('batchCheck', function (): void {
        test('performs batch authorization checks successfully', function (): void {
            $store = 'test-store';
            $model = 'test-model';
            $checks = new BatchCheckItems([
                new BatchCheckItem(
                    correlationId: 'check1',
                    tupleKey: new TupleKey(
                        user: 'user:anne',
                        relation: 'reader',
                        object: 'document:budget',
                    ),
                ),
                new BatchCheckItem(
                    correlationId: 'check2',
                    tupleKey: new TupleKey(
                        user: 'user:bob',
                        relation: 'writer',
                        object: 'document:roadmap',
                    ),
                ),
            ]);

            $this->mockStream->method('__toString')->willReturn('{"results": {"check1": {"allowed": true}, "check2": {"allowed": false}}}');

            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->with(test()->isInstanceOf(BatchCheckRequest::class))
                ->willReturn($this->mockResponse);

            $this->httpService
                ->expects(test()->once())
                ->method('getLastRequest')
                ->willReturn($this->mockRequest);

            $result = $this->service->batchCheck($store, $model, $checks);

            expect($result)->toBeInstanceOf(Success::class);
            expect($result->unwrap())->toBeInstanceOf(BatchCheckResponse::class);
        });
    });
});
