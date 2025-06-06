<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use OpenFGA\Client;
use OpenFGA\Events\{EventDispatcher, HttpRequestSentEvent, HttpResponseReceivedEvent, OperationCompletedEvent, OperationStartedEvent};
use OpenFGA\Observability\NoOpTelemetryProvider;
use Psr\Http\Message\{RequestInterface, ResponseInterface, StreamInterface, UriInterface};

/**
 * Example: Event-Driven Telemetry with Custom Listeners.
 *
 * This example demonstrates how to use the event-driven telemetry system
 * to create custom observability solutions without tight coupling.
 */

// Create a custom event listener for logging
final class LoggingEventListener
{
    public function onHttpRequestSent(HttpRequestSentEvent $event): void
    {
        echo '[' . date('Y-m-d H:i:s') . "] HTTP Request Sent:\n";
        echo "  Operation: {$event->getOperation()}\n";
        echo "  Method: {$event->getRequest()->getMethod()}\n";
        echo "  URL: {$event->getRequest()->getUri()}\n";
        echo "  Store ID: {$event->getStoreId()}\n";
        echo "\n";
    }

    public function onHttpResponseReceived(HttpResponseReceivedEvent $event): void
    {
        echo '[' . date('Y-m-d H:i:s') . "] HTTP Response Received:\n";
        echo "  Operation: {$event->getOperation()}\n";
        echo '  Success: ' . ($event->isSuccessful() ? 'Yes' : 'No') . "\n";

        if ($event->getResponse()) {
            echo "  Status Code: {$event->getResponse()->getStatusCode()}\n";
        }

        if ($event->getException()) {
            echo "  Error: {$event->getException()->getMessage()}\n";
        }
        echo "\n";
    }

    public function onOperationCompleted(OperationCompletedEvent $event): void
    {
        echo '[' . date('Y-m-d H:i:s') . "] Operation Completed:\n";
        echo "  Operation: {$event->getOperation()}\n";
        echo '  Success: ' . ($event->isSuccessful() ? 'Yes' : 'No') . "\n";

        if ($event->getException()) {
            echo "  Error: {$event->getException()->getMessage()}\n";
        }
        echo "\n";
    }

    public function onOperationStarted(OperationStartedEvent $event): void
    {
        echo '[' . date('Y-m-d H:i:s') . "] Operation Started:\n";
        echo "  Operation: {$event->getOperation()}\n";
        echo "  Store ID: {$event->getStoreId()}\n";
        echo "  Model ID: {$event->getModelId()}\n";
        echo "\n";
    }
}

// Create a metrics collection listener
final class MetricsEventListener
{
    private array $operationTimes = [];

    private array $requestCounts = [];

    public function getMetrics(): array
    {
        return [
            'request_counts' => $this->requestCounts,
            'active_operations' => count($this->operationTimes),
        ];
    }

    public function onOperationCompleted(OperationCompletedEvent $event): void
    {
        $operation = $event->getOperation();

        // Count operations
        if (! isset($this->requestCounts[$operation])) {
            $this->requestCounts[$operation] = 0;
        }
        $this->requestCounts[$operation]++;

        // Track timing if we have start time
        if (isset($this->operationTimes[$event->getEventId()])) {
            $duration = microtime(true) - $this->operationTimes[$event->getEventId()];
            echo "[METRICS] {$operation} completed in " . round($duration * 1000, 2) . "ms\n";
            unset($this->operationTimes[$event->getEventId()]);
        }
    }

    public function onOperationStarted(OperationStartedEvent $event): void
    {
        $this->operationTimes[$event->getEventId()] = microtime(true);
    }
}

function demonstrateEventDrivenTelemetry(): void
{
    echo "OpenFGA Event-Driven Telemetry Example\n";
    echo "======================================\n\n";

    // Create the client using the fromOptions factory method
    $client = Client::fromOptions(
        url: 'https://api.fga.example',
        telemetry: new NoOpTelemetryProvider,
    );

    // Note: In a real implementation, you would need to configure the event dispatcher
    // through the ServiceProvider. This example shows the concept.

    echo "Setting up custom event listeners...\n\n";

    // Create event dispatcher and listeners
    $eventDispatcher = new EventDispatcher;
    $loggingListener = new LoggingEventListener;
    $metricsListener = new MetricsEventListener;

    // Register listeners for different events
    $eventDispatcher->addListener(HttpRequestSentEvent::class, [$loggingListener, 'onHttpRequestSent']);
    $eventDispatcher->addListener(HttpResponseReceivedEvent::class, [$loggingListener, 'onHttpResponseReceived']);
    $eventDispatcher->addListener(OperationStartedEvent::class, [$loggingListener, 'onOperationStarted']);
    $eventDispatcher->addListener(OperationCompletedEvent::class, [$loggingListener, 'onOperationCompleted']);

    // Register metrics listener
    $eventDispatcher->addListener(OperationStartedEvent::class, [$metricsListener, 'onOperationStarted']);
    $eventDispatcher->addListener(OperationCompletedEvent::class, [$metricsListener, 'onOperationCompleted']);

    echo "Demonstrating event emission (these would normally be triggered by actual API calls)...\n\n";

    // Simulate some events (in real usage, these would be emitted by the HttpService)
    // Note: In a real implementation, these would be proper PSR-7 objects
    $mockRequest = new class implements RequestInterface {
        public function getBody(): StreamInterface
        {
            return new class implements StreamInterface {
                public function getSize(): ?int
                {
                    return 256;
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

                public function tell(): int
                {
                    return 0;
                }

                public function eof(): bool
                {
                    return true;
                }

                public function isSeekable(): bool
                {
                    return false;
                }

                public function seek(int $offset, int $whence = SEEK_SET): void
                {
                }

                public function rewind(): void
                {
                }

                public function isWritable(): bool
                {
                    return false;
                }

                public function write(string $string): int
                {
                    return 0;
                }

                public function isReadable(): bool
                {
                    return false;
                }

                public function read(int $length): string
                {
                    return '';
                }

                public function getContents(): string
                {
                    return '';
                }

                public function getMetadata(?string $key = null)
                {
                    return null;
                }
            };
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
            return 'POST';
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
            return new class implements UriInterface {
                public function __toString(): string
                {
                    return 'https://api.fga.example/stores/store-123/check';
                }

                public function getScheme(): string
                {
                    return 'https';
                }

                public function getAuthority(): string
                {
                    return 'api.fga.example';
                }

                public function getUserInfo(): string
                {
                    return '';
                }

                public function getHost(): string
                {
                    return 'api.fga.example';
                }

                public function getPort(): ?int
                {
                    return null;
                }

                public function getPath(): string
                {
                    return '/stores/store-123/check';
                }

                public function getQuery(): string
                {
                    return '';
                }

                public function getFragment(): string
                {
                    return '';
                }

                public function withScheme(string $scheme): UriInterface
                {
                    return $this;
                }

                public function withUserInfo(string $user, ?string $password = null): UriInterface
                {
                    return $this;
                }

                public function withHost(string $host): UriInterface
                {
                    return $this;
                }

                public function withPort(?int $port): UriInterface
                {
                    return $this;
                }

                public function withPath(string $path): UriInterface
                {
                    return $this;
                }

                public function withQuery(string $query): UriInterface
                {
                    return $this;
                }

                public function withFragment(string $fragment): UriInterface
                {
                    return $this;
                }
            };
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
    };

    $mockResponse = new class implements ResponseInterface {
        public function getBody(): StreamInterface
        {
            return new class implements StreamInterface {
                public function getSize(): ?int
                {
                    return 128;
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

                public function tell(): int
                {
                    return 0;
                }

                public function eof(): bool
                {
                    return true;
                }

                public function isSeekable(): bool
                {
                    return false;
                }

                public function seek(int $offset, int $whence = SEEK_SET): void
                {
                }

                public function rewind(): void
                {
                }

                public function isWritable(): bool
                {
                    return false;
                }

                public function write(string $string): int
                {
                    return 0;
                }

                public function isReadable(): bool
                {
                    return false;
                }

                public function read(int $length): string
                {
                    return '';
                }

                public function getContents(): string
                {
                    return '';
                }

                public function getMetadata(?string $key = null)
                {
                    return null;
                }
            };
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
            return 200;
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
    };

    // Simulate operation lifecycle
    $startEvent = new OperationStartedEvent(
        operation: 'check',
        storeId: 'store-123',
        modelId: 'model-456',
        context: ['trace' => true],
    );
    $eventDispatcher->dispatch($startEvent);

    $requestEvent = new HttpRequestSentEvent(
        request: $mockRequest,
        operation: 'check',
        storeId: 'store-123',
        modelId: 'model-456',
    );
    $eventDispatcher->dispatch($requestEvent);

    // Simulate brief delay
    usleep(50000); // 50ms

    $responseEvent = new HttpResponseReceivedEvent(
        request: $mockRequest,
        response: $mockResponse,
        operation: 'check',
        storeId: 'store-123',
        modelId: 'model-456',
    );
    $eventDispatcher->dispatch($responseEvent);

    $completedEvent = new OperationCompletedEvent(
        operation: 'check',
        success: true,
        storeId: 'store-123',
        modelId: 'model-456',
        context: ['trace' => true],
        result: new stdClass,
    );
    $eventDispatcher->dispatch($completedEvent);

    // Show collected metrics
    echo "Collected Metrics:\n";
    echo "==================\n";
    $metrics = $metricsListener->getMetrics();
    echo json_encode($metrics, JSON_PRETTY_PRINT) . "\n\n";

    echo "Event-driven telemetry allows you to:\n";
    echo "- Decouple observability from business logic\n";
    echo "- Add multiple listeners for the same events\n";
    echo "- Create specialized listeners for different concerns\n";
    echo "- Easily test telemetry functionality in isolation\n";
    echo "- Replace or enhance telemetry without changing core code\n";
}

// Run the demonstration
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    demonstrateEventDrivenTelemetry();
}
