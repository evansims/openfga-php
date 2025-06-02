<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Observability;

use DateTimeImmutable;
use Exception;
use OpenFGA\Models\{AuthorizationModel, Store};
use OpenFGA\Models\Collections\TypeDefinitions;
use OpenFGA\Models\Enums\SchemaVersion;
use OpenFGA\Observability\OpenTelemetryProvider;
use OpenFGA\Tests\Support\Responses\SimpleResponse;
use Psr\Http\Message\{RequestInterface, UriInterface};
use Throwable;

use function is_array;

describe('OpenTelemetryProvider', function (): void {
    beforeEach(function (): void {
        // Create simple mock objects that implement the minimum needed methods
        $this->tracer = new class {
            public function spanBuilder(string $name): object
            {
                return new class {
                    public function setSpanKind(int $spanKind): static
                    {
                        return $this;
                    }

                    public function startSpan(): object
                    {
                        return new class {
                            public array $attributes = [];

                            public ?string $status = null;

                            public ?string $statusDescription = null;

                            public ?Throwable $recordedException = null;

                            public bool $ended = false;

                            public array $events = [];

                            public function setAttribute(string $key, mixed $value): static
                            {
                                $this->attributes[$key] = $value;

                                return $this;
                            }

                            public function setAttributes(mixed $attributes): static
                            {
                                if (is_array($attributes)) {
                                    $this->attributes = array_merge($this->attributes, $attributes);
                                }

                                return $this;
                            }

                            public function setStatus(string $status, string $description = ''): static
                            {
                                $this->status = $status;
                                $this->statusDescription = $description;

                                return $this;
                            }

                            public function recordException(Throwable $exception): static
                            {
                                $this->recordedException = $exception;

                                return $this;
                            }

                            public function addEvent(string $name, mixed $attributes = null): static
                            {
                                $this->events[] = ['name' => $name, 'attributes' => $attributes];

                                return $this;
                            }

                            public function getAttribute(string $key): mixed
                            {
                                return $this->attributes[$key] ?? null;
                            }

                            public function end(): void
                            {
                                $this->ended = true;
                            }
                        };
                    }
                };
            }
        };

        $this->meter = new class {
            public function createCounter(string $name, string $unit, string $description): object
            {
                return new class {
                    public function add(int $amount, mixed $attributes = null): void
                    {
                    }
                };
            }

            public function createHistogram(string $name, string $unit, string $description): object
            {
                return new class {
                    public function record(float $amount, mixed $attributes = null): void
                    {
                    }
                };
            }
        };

        $this->provider = new OpenTelemetryProvider($this->tracer, $this->meter);
    });

    test('constructs with tracer and meter', function (): void {
        $provider = new OpenTelemetryProvider($this->tracer, $this->meter);

        expect($provider)->toBeInstanceOf(OpenTelemetryProvider::class);
    });

    test('starts HTTP request span without throwing', function (): void {
        $uri = $this->createMock(UriInterface::class);
        $uri->method('__toString')->willReturn('https://api.openfga.example/stores/test');
        $uri->method('getScheme')->willReturn('https');
        $uri->method('getHost')->willReturn('api.openfga.example');
        $uri->method('getPath')->willReturn('/stores/test');
        $uri->method('getQuery')->willReturn('');
        $uri->method('getPort')->willReturn(null);

        $request = $this->createMock(RequestInterface::class);
        $request->method('getMethod')->willReturn('POST');
        $request->method('getUri')->willReturn($uri);
        $request->method('getHeaderLine')->with('User-Agent')->willReturn('');

        $span = $this->provider->startHttpRequest($request);

        expect($span)->toBeObject();
    });

    test('starts operation span with store objects', function (): void {
        $store = new Store(
            id: 'store-123',
            name: 'Test Store',
            createdAt: new DateTimeImmutable,
            updatedAt: new DateTimeImmutable,
        );

        $model = new AuthorizationModel(
            id: 'model-456',
            schemaVersion: SchemaVersion::V1_1,
            typeDefinitions: new TypeDefinitions([]),
        );

        $span = $this->provider->startOperation('check', $store, $model);

        expect($span)->toBeObject();
    });

    test('starts operation span with string identifiers', function (): void {
        $span = $this->provider->startOperation('expand', 'store-789', 'model-101');

        expect($span)->toBeObject();
    });

    test('ends HTTP request span with response', function (): void {
        $response = new SimpleResponse(statusCode: 200);
        $span = new class {
            public function end(): void
            {
            }

            public function getAttribute(string $key): mixed
            {
                return 'GET';
            }

            public function setAttribute(string $key, mixed $value): static
            {
                return $this;
            }

            public function setStatus(string $status, string $description = ''): static
            {
                return $this;
            }
        };

        // Should not throw
        $this->provider->endHttpRequest($span, $response);

        expect(true)->toBeTrue();
    });

    test('ends HTTP request span with exception', function (): void {
        $exception = new Exception('Network error');
        $span = new class {
            public function end(): void
            {
            }

            public function getAttribute(string $key): mixed
            {
                return 'GET';
            }

            public function recordException(Throwable $exception): static
            {
                return $this;
            }

            public function setStatus(string $status, string $description = ''): static
            {
                return $this;
            }
        };

        // Should not throw
        $this->provider->endHttpRequest($span, null, $exception);

        expect(true)->toBeTrue();
    });

    test('ends operation span with success', function (): void {
        $span = new class {
            public function end(): void
            {
            }

            public function getAttribute(string $key): mixed
            {
                return match ($key) {
                    'openfga.operation' => 'check',
                    'openfga.store_id' => 'store-123',
                    default => null,
                };
            }

            public function setAttribute(string $key, mixed $value): static
            {
                return $this;
            }

            public function setStatus(string $status, string $description = ''): static
            {
                return $this;
            }
        };

        // Should not throw
        $this->provider->endOperation($span, true);

        expect(true)->toBeTrue();
    });

    test('records authentication event', function (): void {
        // Should not throw
        $this->provider->recordAuthenticationEvent('token_refresh', true, 0.5);

        expect(true)->toBeTrue();
    });

    test('records circuit breaker state', function (): void {
        // Should not throw
        $this->provider->recordCircuitBreakerState('https://api.example.com', 'open', 5, 0.8);

        expect(true)->toBeTrue();
    });

    test('records operation metrics', function (): void {
        $store = new Store(
            id: 'store-456',
            name: 'Test Store',
            createdAt: new DateTimeImmutable,
            updatedAt: new DateTimeImmutable,
        );

        // Should not throw
        $this->provider->recordOperationMetrics('check', 0.3, $store, 'model-789');

        expect(true)->toBeTrue();
    });

    test('records retry attempt', function (): void {
        // Should not throw
        $this->provider->recordRetryAttempt('https://api.example.com/check', 2, 1000, 'success');

        expect(true)->toBeTrue();
    });

    test('handles graceful degradation with no-op objects', function (): void {
        // Create provider with objects that don't have expected methods
        $badTracer = new class {};
        $badMeter = new class {};

        // Should not throw and should create no-op objects
        $provider = new OpenTelemetryProvider($badTracer, $badMeter);

        expect($provider)->toBeInstanceOf(OpenTelemetryProvider::class);

        // Should handle all operations gracefully without errors
        $provider->recordAuthenticationEvent('test', true, 0.1);
        $provider->recordCircuitBreakerState('test', 'closed', 0, 0.0);
        $provider->recordRetryAttempt('test', 1, 100, 'success');
        $provider->recordOperationMetrics('test', 0.1, 'test-store');

        expect(true)->toBeTrue();
    });

    test('handles null spans gracefully', function (): void {
        // Should not throw when passing null spans
        $this->provider->endHttpRequest(null);
        $this->provider->endOperation(null, true);

        expect(true)->toBeTrue();
    });

    test('handles invalid span objects gracefully', function (): void {
        // Should not throw when passing non-object spans
        $this->provider->endHttpRequest('not an object');
        $this->provider->endOperation('not an object', false);

        expect(true)->toBeTrue();
    });
});
