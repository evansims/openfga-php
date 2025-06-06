<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Observability;

use OpenFGA\Events\{HttpRequestSentEvent, OperationCompletedEvent, OperationStartedEvent};
use OpenFGA\Observability\{TelemetryEventListener, TelemetryInterface};
use Psr\Http\Message\{RequestInterface, ResponseInterface, StreamInterface, UriInterface};
use stdClass;
use Throwable;

// Mock classes for testing
final class MockUri implements UriInterface
{
    public function __construct(private string $uri)
    {
    }

    public function __toString(): string
    {
        return $this->uri;
    }

    public function getAuthority(): string
    {
        return 'api.openfga.example';
    }

    public function getFragment(): string
    {
        return '';
    }

    public function getHost(): string
    {
        return 'api.openfga.example';
    }

    public function getPath(): string
    {
        return '/stores/store-123/check';
    }

    public function getPort(): ?int
    {
        return null;
    }

    public function getQuery(): string
    {
        return '';
    }

    public function getScheme(): string
    {
        return 'https';
    }

    public function getUserInfo(): string
    {
        return '';
    }

    public function withFragment(string $fragment): UriInterface
    {
        return $this;
    }

    public function withHost(string $host): UriInterface
    {
        return $this;
    }

    public function withPath(string $path): UriInterface
    {
        return $this;
    }

    public function withPort(?int $port): UriInterface
    {
        return $this;
    }

    public function withQuery(string $query): UriInterface
    {
        return $this;
    }

    public function withScheme(string $scheme): UriInterface
    {
        return $this;
    }

    public function withUserInfo(string $user, ?string $password = null): UriInterface
    {
        return $this;
    }
}

final class MockStream implements StreamInterface
{
    public function __construct(private int $size)
    {
    }

    public function __toString(): string
    {
        return '';
    }

    public function close(): void
    {
    }

    public function detach()
    {
        return null;
    }

    public function eof(): bool
    {
        return true;
    }

    public function getContents(): string
    {
        return '';
    }

    public function getMetadata(?string $key = null)
    {
        return null;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function isReadable(): bool
    {
        return false;
    }

    public function isSeekable(): bool
    {
        return false;
    }

    public function isWritable(): bool
    {
        return false;
    }

    public function read(int $length): string
    {
        return '';
    }

    public function rewind(): void
    {
    }

    public function seek(int $offset, int $whence = SEEK_SET): void
    {
    }

    public function tell(): int
    {
        return 0;
    }

    public function write(string $string): int
    {
        return 0;
    }
}

final class MockRequest implements RequestInterface
{
    public function __construct(
        private string $method,
        private string $uri,
        private int $bodySize = 0,
    ) {
    }

    public function getBody(): StreamInterface
    {
        return new MockStream($this->bodySize);
    }

    public function getHeader(string $name): array
    {
        return [];
    }

    public function getHeaderLine(string $name): string
    {
        return '';
    }

    public function getHeaders(): array
    {
        return [];
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getProtocolVersion(): string
    {
        return '1.1';
    }

    public function getRequestTarget(): string
    {
        return '/';
    }

    public function getUri(): UriInterface
    {
        return new MockUri($this->uri);
    }

    public function hasHeader(string $name): bool
    {
        return false;
    }

    public function withAddedHeader(string $name, $value): static
    {
        return $this;
    }

    public function withBody(StreamInterface $body): static
    {
        return $this;
    }

    public function withHeader(string $name, $value): static
    {
        return $this;
    }

    public function withMethod(string $method): static
    {
        return $this;
    }

    public function withoutHeader(string $name): static
    {
        return $this;
    }

    public function withProtocolVersion(string $version): static
    {
        return $this;
    }

    public function withRequestTarget(string $requestTarget): static
    {
        return $this;
    }

    public function withUri(UriInterface $uri, bool $preserveHost = false): static
    {
        return $this;
    }
}

final class MockResponse implements ResponseInterface
{
    public function __construct(
        private int $statusCode,
        private int $bodySize = 0,
    ) {
    }

    public function getBody(): StreamInterface
    {
        return new MockStream($this->bodySize);
    }

    public function getHeader(string $name): array
    {
        return [];
    }

    public function getHeaderLine(string $name): string
    {
        return '';
    }

    public function getHeaders(): array
    {
        return [];
    }

    public function getProtocolVersion(): string
    {
        return '1.1';
    }

    public function getReasonPhrase(): string
    {
        return '';
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function hasHeader(string $name): bool
    {
        return false;
    }

    public function withAddedHeader(string $name, $value): static
    {
        return $this;
    }

    public function withBody(StreamInterface $body): static
    {
        return $this;
    }

    public function withHeader(string $name, $value): static
    {
        return $this;
    }

    public function withoutHeader(string $name): static
    {
        return $this;
    }

    public function withProtocolVersion(string $version): static
    {
        return $this;
    }

    public function withStatus(int $code, string $reasonPhrase = ''): static
    {
        return $this;
    }
}

final class MockTelemetry implements TelemetryInterface
{
    public array $recordedSpans = [];

    public function endHttpRequest(object | null $span, ?ResponseInterface $response = null, ?Throwable $exception = null): void
    {
    }

    public function endOperation(object | null $span, bool $success, ?Throwable $exception = null, array $attributes = []): void
    {
    }

    public function recordAuthenticationEvent(string $event, bool $success, float $duration, array $attributes = []): void
    {
    }

    public function recordCircuitBreakerState(string $endpoint, string $state, int $failures, float $failureRate): void
    {
    }

    public function recordOperationMetrics(string $operation, float $duration, $store, $model = null, array $attributes = []): void
    {
    }

    public function recordRetryAttempt(string $endpoint, int $attempt, int $delayMs, string $outcome, ?Throwable $exception = null): void
    {
    }

    public function recordSpan(string $name, array $attributes = []): void
    {
        $this->recordedSpans[] = ['name' => $name, 'attributes' => $attributes];
    }

    public function startHttpRequest(RequestInterface $request): object | null
    {
        return null;
    }

    public function startOperation(string $operation, $store, $model = null, array $attributes = []): object | null
    {
        return null;
    }
}

describe('TelemetryEventListener', function (): void {
    test('onHttpRequestSent records request telemetry', function (): void {
        $telemetry = new MockTelemetry;
        $listener = new TelemetryEventListener($telemetry);

        $request = new MockRequest('POST', 'https://api.openfga.example/stores/store-123/check', 1024);

        $event = new HttpRequestSentEvent(
            request: $request,
            operation: 'check',
            storeId: 'store-123',
            modelId: 'model-456',
        );

        $listener->onHttpRequestSent($event);

        expect($telemetry->recordedSpans)->toHaveCount(1);
        expect($telemetry->recordedSpans[0]['name'])->toBe('http.request.sent');
        expect($telemetry->recordedSpans[0]['attributes']['http.method'])->toBe('POST');
        expect($telemetry->recordedSpans[0]['attributes']['openfga.operation'])->toBe('check');
        expect($telemetry->recordedSpans[0]['attributes']['openfga.store_id'])->toBe('store-123');
    });

    test('onOperationStarted records operation start telemetry', function (): void {
        $telemetry = new MockTelemetry;
        $listener = new TelemetryEventListener($telemetry);

        $event = new OperationStartedEvent(
            operation: 'check',
            storeId: 'store-123',
            modelId: 'model-456',
            context: ['trace' => true],
        );

        $listener->onOperationStarted($event);

        expect($telemetry->recordedSpans)->toHaveCount(1);
        expect($telemetry->recordedSpans[0]['name'])->toBe('openfga.operation.started');
        expect($telemetry->recordedSpans[0]['attributes']['openfga.operation'])->toBe('check');
        expect($telemetry->recordedSpans[0]['attributes']['openfga.store_id'])->toBe('store-123');
    });

    test('onOperationCompleted records successful operation telemetry', function (): void {
        $telemetry = new MockTelemetry;
        $listener = new TelemetryEventListener($telemetry);

        $result = new stdClass;

        $event = new OperationCompletedEvent(
            operation: 'expand',
            success: true,
            storeId: 'store-123',
            context: ['consistency' => 'eventual'],
            result: $result,
        );

        $listener->onOperationCompleted($event);

        expect($telemetry->recordedSpans)->toHaveCount(1);
        expect($telemetry->recordedSpans[0]['name'])->toBe('openfga.operation.completed');
        expect($telemetry->recordedSpans[0]['attributes']['openfga.operation'])->toBe('expand');
        expect($telemetry->recordedSpans[0]['attributes']['openfga.operation.success'])->toBeTrue();
    });
});
