<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Services;

use Exception;
use OpenFGA\Authentication\AuthenticationInterface;
use OpenFGA\Network\{RequestContext, RequestMethod};
use OpenFGA\Services\{AuthenticationService, AuthenticationServiceInterface, TelemetryServiceInterface};
use Psr\Http\Message\{ResponseInterface, StreamFactoryInterface};

use function is_float;

beforeEach(function (): void {
    $this->mockAuthentication = test()->createMock(AuthenticationInterface::class);
    $this->mockTelemetryService = test()->createMock(TelemetryServiceInterface::class);
    $this->mockStreamFactory = test()->createMock(StreamFactoryInterface::class);
    $this->mockResponse = test()->createMock(ResponseInterface::class);

    $this->service = new AuthenticationService(
        $this->mockAuthentication,
        $this->mockTelemetryService,
    );

    $this->serviceWithoutTelemetry = new AuthenticationService($this->mockAuthentication);
    $this->serviceWithoutAuth = new AuthenticationService;
});

describe('AuthenticationService', function (): void {
    it('implements AuthenticationServiceInterface', function (): void {
        expect($this->service)->toBeInstanceOf(AuthenticationServiceInterface::class);
    });

    describe('getAuthorizationHeader', function (): void {
        it('returns null when no authentication is configured', function (): void {
            $result = $this->serviceWithoutAuth->getAuthorizationHeader($this->mockStreamFactory);

            expect($result)->toBeNull();
        });

        it('returns cached header when available', function (): void {
            $cachedHeader = 'Bearer cached-token';

            $this->mockAuthentication
                ->expects(test()->once())
                ->method('getAuthorizationHeader')
                ->willReturn($cachedHeader);

            $result = $this->service->getAuthorizationHeader($this->mockStreamFactory);

            expect($result)->toBe($cachedHeader);
        });

        it('returns null when authentication request is null', function (): void {
            $this->mockAuthentication
                ->expects(test()->once())
                ->method('getAuthorizationHeader')
                ->willReturn(null);

            $this->mockAuthentication
                ->expects(test()->once())
                ->method('getAuthenticationRequest')
                ->with($this->mockStreamFactory)
                ->willReturn(null);

            $result = $this->service->getAuthorizationHeader($this->mockStreamFactory);

            expect($result)->toBeNull();
        });

        it('returns null when no request sender provided for token refresh', function (): void {
            $requestContext = new RequestContext(RequestMethod::POST, '/oauth/token');

            $this->mockAuthentication
                ->expects(test()->once())
                ->method('getAuthorizationHeader')
                ->willReturn(null);

            $this->mockAuthentication
                ->expects(test()->once())
                ->method('getAuthenticationRequest')
                ->with($this->mockStreamFactory)
                ->willReturn($requestContext);

            $result = $this->service->getAuthorizationHeader($this->mockStreamFactory);

            expect($result)->toBeNull();
        });

        it('returns token after successful authentication request', function (): void {
            $requestContext = new RequestContext(RequestMethod::POST, '/oauth/token');
            $requestSender = fn ($context) => $this->mockResponse;

            // Setup mock expectations for standard authentication
            $this->mockAuthentication
                ->expects(test()->exactly(2))
                ->method('getAuthorizationHeader')
                ->willReturnOnConsecutiveCalls(null, 'Bearer new-token'); // No cached token, then token after refresh

            $this->mockAuthentication
                ->expects(test()->once())
                ->method('getAuthenticationRequest')
                ->with($this->mockStreamFactory)
                ->willReturn($requestContext);

            // Expect telemetry recording
            $this->mockTelemetryService
                ->expects(test()->once())
                ->method('recordAuthenticationEvent');

            $result = $this->service->getAuthorizationHeader($this->mockStreamFactory, $requestSender);

            expect($result)->toBe('Bearer new-token');
        });

        it('returns null and records telemetry on authentication failure', function (): void {
            $requestContext = new RequestContext(RequestMethod::POST, '/oauth/token');
            $exception = new Exception('Auth failed');
            $requestSender = function ($context) use ($exception): void {
                throw $exception;
            };

            // Setup mock expectations
            $this->mockAuthentication
                ->expects(test()->once())
                ->method('getAuthorizationHeader')
                ->willReturn(null);

            $this->mockAuthentication
                ->expects(test()->once())
                ->method('getAuthenticationRequest')
                ->with($this->mockStreamFactory)
                ->willReturn($requestContext);

            // Expect telemetry recording for auth failure
            $this->mockTelemetryService
                ->expects(test()->once())
                ->method('recordAuthenticationEvent');

            $result = $this->service->getAuthorizationHeader($this->mockStreamFactory, $requestSender);

            expect($result)->toBeNull();
        });

        it('works without telemetry service', function (): void {
            $cachedHeader = 'Bearer no-telemetry-token';

            $this->mockAuthentication
                ->expects(test()->once())
                ->method('getAuthorizationHeader')
                ->willReturn($cachedHeader);

            $result = $this->serviceWithoutTelemetry->getAuthorizationHeader($this->mockStreamFactory);

            expect($result)->toBe($cachedHeader);
        });
    });

    describe('sendAuthenticationRequest', function (): void {
        it('sends request and records successful telemetry', function (): void {
            $requestContext = new RequestContext(RequestMethod::POST, '/oauth/token');
            $requestSender = function ($context) use ($requestContext) {
                expect($context)->toBe($requestContext);

                return $this->mockResponse;
            };

            // Expect successful telemetry recording
            $this->mockTelemetryService
                ->expects(test()->once())
                ->method('recordAuthenticationEvent')
                ->with(
                    'auth_http_request',
                    true,
                    test()->callback(fn ($duration) => is_float($duration) && 0 <= $duration),
                    test()->callback(fn ($attrs) => 'POST' === $attrs['method'] && '/oauth/token' === $attrs['url']),
                );

            $result = $this->service->sendAuthenticationRequest($requestContext, $requestSender);

            expect($result)->toBe($this->mockResponse);
        });

        it('records failed telemetry and rethrows exception', function (): void {
            $requestContext = new RequestContext(RequestMethod::POST, '/oauth/token');
            $exception = new Exception('Request failed');
            $requestSender = function ($context) use ($exception): void {
                throw $exception;
            };

            // Expect failed telemetry recording
            $this->mockTelemetryService
                ->expects(test()->once())
                ->method('recordAuthenticationEvent')
                ->with(
                    'auth_http_request',
                    false,
                    test()->callback(fn ($duration) => is_float($duration) && 0 <= $duration),
                    test()->callback(
                        fn ($attrs) => 'POST' === $attrs['method']
                        && '/oauth/token' === $attrs['url']
                        && 'Request failed' === $attrs['error'],
                    ),
                );

            expect(fn () => $this->service->sendAuthenticationRequest($requestContext, $requestSender))
                ->toThrow(Exception::class, 'Request failed');
        });

        it('works without telemetry service', function (): void {
            $requestContext = new RequestContext(RequestMethod::GET, '/auth');
            $requestSender = fn ($context) => $this->mockResponse;

            $result = $this->serviceWithoutTelemetry->sendAuthenticationRequest($requestContext, $requestSender);

            expect($result)->toBe($this->mockResponse);
        });
    });
});
