<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Services;

use OpenFGA\Models\TupleKeyInterface;
use OpenFGA\Network\RequestManagerInterface;
use OpenFGA\Requests\CheckRequest;
use OpenFGA\Services\HttpService;
use Psr\Http\Message\{RequestInterface as HttpRequestInterface, ResponseInterface as HttpResponseInterface};

describe('HttpService', function (): void {
    beforeEach(function (): void {
        $this->mockRequestManager = test()->createMock(RequestManagerInterface::class);
        $this->mockHttpRequest = test()->createMock(HttpRequestInterface::class);
        $this->mockHttpResponse = test()->createMock(HttpResponseInterface::class);

        $this->service = new HttpService($this->mockRequestManager);
    });

    describe('send()', function (): void {
        test('converts OpenFGA request to HTTP request and sends it', function (): void {
            $checkRequest = new CheckRequest(
                store: 'store-123',
                model: 'model-456',
                tupleKey: test()->createMock(TupleKeyInterface::class),
            );

            $this->mockRequestManager
                ->expects(test()->once())
                ->method('request')
                ->with($checkRequest)
                ->willReturn($this->mockHttpRequest);

            $this->mockRequestManager
                ->expects(test()->once())
                ->method('send')
                ->with($this->mockHttpRequest)
                ->willReturn($this->mockHttpResponse);

            $response = $this->service->send($checkRequest);

            expect($response)->toBe($this->mockHttpResponse);
        });

        test('tracks last request and response', function (): void {
            $checkRequest = new CheckRequest(
                store: 'store-123',
                model: 'model-456',
                tupleKey: test()->createMock(TupleKeyInterface::class),
            );

            $this->mockRequestManager
                ->method('request')
                ->willReturn($this->mockHttpRequest);

            $this->mockRequestManager
                ->method('send')
                ->willReturn($this->mockHttpResponse);

            // Initially null
            expect($this->service->getLastRequest())->toBeNull();
            expect($this->service->getLastResponse())->toBeNull();

            // Send request
            $this->service->send($checkRequest);

            // Now populated
            expect($this->service->getLastRequest())->toBe($this->mockHttpRequest);
            expect($this->service->getLastResponse())->toBe($this->mockHttpResponse);
        });

        test('updates last request/response on each send', function (): void {
            $checkRequest1 = new CheckRequest(
                store: 'store-1',
                model: 'model-1',
                tupleKey: test()->createMock(TupleKeyInterface::class),
            );

            $checkRequest2 = new CheckRequest(
                store: 'store-2',
                model: 'model-2',
                tupleKey: test()->createMock(TupleKeyInterface::class),
            );

            $mockHttpRequest1 = test()->createMock(HttpRequestInterface::class);
            $mockHttpResponse1 = test()->createMock(HttpResponseInterface::class);
            $mockHttpRequest2 = test()->createMock(HttpRequestInterface::class);
            $mockHttpResponse2 = test()->createMock(HttpResponseInterface::class);

            $this->mockRequestManager
                ->method('request')
                ->willReturnOnConsecutiveCalls($mockHttpRequest1, $mockHttpRequest2);

            $this->mockRequestManager
                ->method('send')
                ->willReturnOnConsecutiveCalls($mockHttpResponse1, $mockHttpResponse2);

            // First request
            $this->service->send($checkRequest1);
            expect($this->service->getLastRequest())->toBe($mockHttpRequest1);
            expect($this->service->getLastResponse())->toBe($mockHttpResponse1);

            // Second request
            $this->service->send($checkRequest2);
            expect($this->service->getLastRequest())->toBe($mockHttpRequest2);
            expect($this->service->getLastResponse())->toBe($mockHttpResponse2);
        });
    });

    describe('getLastRequest()', function (): void {
        test('returns null when no requests have been sent', function (): void {
            expect($this->service->getLastRequest())->toBeNull();
        });
    });

    describe('getLastResponse()', function (): void {
        test('returns null when no responses have been received', function (): void {
            expect($this->service->getLastResponse())->toBeNull();
        });
    });
});
