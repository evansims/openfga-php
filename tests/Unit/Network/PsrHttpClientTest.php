<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Network;

use Exception;
use OpenFGA\Network\PsrHttpClient;
use Psr\Http\Client\{ClientExceptionInterface, ClientInterface};
use Psr\Http\Message\{RequestInterface, ResponseInterface};
use RuntimeException;

describe('PsrHttpClient', function (): void {
    beforeEach(function (): void {
        $this->mockPsrClient = test()->createMock(ClientInterface::class);
        $this->mockRequest = test()->createMock(RequestInterface::class);
        $this->mockResponse = test()->createMock(ResponseInterface::class);
    });

    describe('constructor', function (): void {
        test('accepts a PSR-18 client', function (): void {
            $client = new PsrHttpClient($this->mockPsrClient);

            expect($client)->toBeInstanceOf(PsrHttpClient::class);
        });

        test('uses discovery when no client provided', function (): void {
            // This test will use the actual discovery mechanism
            // It should find a client if one is installed (like Guzzle or Symfony HttpClient)
            try {
                $client = new PsrHttpClient;
                expect($client)->toBeInstanceOf(PsrHttpClient::class);
            } catch (RuntimeException $e) {
                // If no client is available, that's expected in some environments
                expect($e->getMessage())->toContain('No PSR-18 HTTP client found');
            }
        });
    });

    describe('send()', function (): void {
        test('delegates to the underlying PSR-18 client', function (): void {
            $this->mockPsrClient
                ->expects(test()->once())
                ->method('sendRequest')
                ->with($this->mockRequest)
                ->willReturn($this->mockResponse);

            $client = new PsrHttpClient($this->mockPsrClient);
            $response = $client->send($this->mockRequest);

            expect($response)->toBe($this->mockResponse);
        });

        test('propagates client exceptions', function (): void {
            $exception = new class('Test exception') extends Exception implements ClientExceptionInterface {};

            $this->mockPsrClient
                ->expects(test()->once())
                ->method('sendRequest')
                ->with($this->mockRequest)
                ->willThrowException($exception);

            $client = new PsrHttpClient($this->mockPsrClient);

            $client->send($this->mockRequest);
        })->throws(Exception::class, 'Test exception');

        test('handles multiple requests', function (): void {
            $mockRequest2 = test()->createMock(RequestInterface::class);
            $mockResponse2 = test()->createMock(ResponseInterface::class);

            $this->mockPsrClient
                ->expects(test()->exactly(2))
                ->method('sendRequest')
                ->willReturnOnConsecutiveCalls($this->mockResponse, $mockResponse2);

            $client = new PsrHttpClient($this->mockPsrClient);

            $response1 = $client->send($this->mockRequest);
            $response2 = $client->send($mockRequest2);

            expect($response1)->toBe($this->mockResponse);
            expect($response2)->toBe($mockResponse2);
        });
    });
});
