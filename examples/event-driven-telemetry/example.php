<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use Buzz\Client\FileGetContents;
use Nyholm\Psr7\Factory\Psr17Factory;
use OpenFGA\Client;
use OpenFGA\Events\{EventDispatcher, HttpRequestSentEvent, HttpResponseReceivedEvent, OperationCompletedEvent, OperationStartedEvent};

use function OpenFGA\{allowed, dsl, model, store, tuple, write};

/*
 * Event-Driven Telemetry Example
 *
 * This example demonstrates how to use the event-driven telemetry system
 * to create custom observability solutions without tight coupling to the
 * main OpenFGA client functionality.
 */

// Create a custom event listener for logging
final class LoggingEventListener
{
    public function onHttpRequestSent(HttpRequestSentEvent $event): void
    {
        echo '[' . date('H:i:s') . "] 📤 HTTP Request: {$event->getOperation()}\n";
        echo "  Method: {$event->getRequest()->getMethod()}\n";
        echo "  URL: {$event->getRequest()->getUri()}\n";
        echo "  Store: {$event->getStoreId()}\n\n";
    }

    public function onHttpResponseReceived(HttpResponseReceivedEvent $event): void
    {
        $status = $event->getResponse() ? $event->getResponse()->getStatusCode() : 'N/A';
        $success = $event->isSuccessful() ? '✅' : '❌';

        echo '[' . date('H:i:s') . "] 📥 HTTP Response: {$event->getOperation()}\n";
        echo "  Status: {$success} {$status}\n";

        if ($event->getException()) {
            echo "  Error: {$event->getException()->getMessage()}\n";
        }
        echo "\n";
    }

    public function onOperationCompleted(OperationCompletedEvent $event): void
    {
        $success = $event->isSuccessful() ? '✅' : '❌';
        echo '[' . date('H:i:s') . "] 🏁 Operation Completed: {$event->getOperation()}\n";
        echo "  Result: {$success}\n";

        if ($event->getException()) {
            echo "  Error: {$event->getException()->getMessage()}\n";
        }
        echo "\n";
    }

    public function onOperationStarted(OperationStartedEvent $event): void
    {
        echo '[' . date('H:i:s') . "] 🚀 Operation Started: {$event->getOperation()}\n";
        echo "  Store: {$event->getStoreId()}\n";
        echo "  Model: {$event->getModelId()}\n\n";
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

    public function onHttpResponseReceived(HttpResponseReceivedEvent $event): void
    {
        // Track HTTP response details but let onOperationCompleted handle timing cleanup
        $response = $event->getResponse();

        if (null !== $response && 400 <= $response->getStatusCode()) {
            echo "⚠️  HTTP Error {$response->getStatusCode()} for [{$event->getOperation()}]\n";
        }
    }

    public function onOperationCompleted(OperationCompletedEvent $event): void
    {
        $operation = $event->getOperation();

        // Count operations
        if (! isset($this->requestCounts[$operation])) {
            $this->requestCounts[$operation] = 0;
        }
        $this->requestCounts[$operation]++;

        // Track timing - always clean up regardless of success/failure
        $operationKey = $this->createOperationKey($event);

        if (isset($this->operationTimes[$operationKey])) {
            $duration = microtime(true) - $this->operationTimes[$operationKey];
            
            if ($event->isSuccessful()) {
                echo "📊 [{$operation}] completed in " . round($duration * 1000, 2) . "ms\n\n";
            } else {
                echo "📊 [{$operation}] failed after " . round($duration * 1000, 2) . "ms\n\n";
            }
            
            // Always clean up the timing entry to prevent memory leaks
            unset($this->operationTimes[$operationKey]);
        }
    }

    public function onOperationStarted(OperationStartedEvent $event): void
    {
        $operationKey = $this->createOperationKey($event);
        $this->operationTimes[$operationKey] = microtime(true);
    }

    private function createOperationKey($event): string
    {
        // Create a composite key using operation, store, and model for consistent identification
        return sprintf(
            '%s:%s:%s',
            $event->getOperation(),
            $event->getStoreId() ?? 'unknown',
            $event->getModelId() ?? 'unknown',
        );
    }
}

$storeId = null;
$client = null;
$exitCode = 0;

try {
    echo "📡 Event-Driven Telemetry Example\n";
    echo "=================================\n\n";

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
    $eventDispatcher->addListener(HttpResponseReceivedEvent::class, [$metricsListener, 'onHttpResponseReceived']);

    // Initialize client with event dispatcher
    $client = new Client(
        url: 'http://localhost:8080',
        httpClient: new FileGetContents(new Psr17Factory),
        httpResponseFactory: new Psr17Factory,
        httpStreamFactory: new Psr17Factory,
        httpRequestFactory: new Psr17Factory,
        eventDispatcher: $eventDispatcher,
    );

    echo "🎬 Performing operations that will trigger events...\n\n";

    // Create workspace - this will trigger events
    $storeId = store($client, 'telemetry-demo');

    // Define authorization model - more events
    $authModel = dsl($client, '
        model
          schema 1.1
        type user
        type document
          relations
            define viewer: [user]
    ');
    $modelId = model($client, $storeId, $authModel);

    // Write relationship - more events
    write($client, $storeId, $modelId, tuple('user:alice', 'viewer', 'document:report'));

    // Check authorization - final events
    $canView = allowed($client, $storeId, $modelId, tuple('user:alice', 'viewer', 'document:report'));
    echo '🎯 Authorization Result: Alice ' . ($canView ? 'CAN' : 'CANNOT') . " view the report\n\n";

    // Show collected metrics
    echo "📊 Collected Metrics:\n";
    echo "====================\n";
    $metrics = $metricsListener->getMetrics();
    echo json_encode($metrics, JSON_PRETTY_PRINT) . "\n\n";

    echo "✨ Event-driven telemetry demonstration complete!\n\n";

    echo "🎯 Key Benefits:\n";
    echo "   • Decouple observability from business logic\n";
    echo "   • Add multiple listeners for the same events\n";
    echo "   • Create specialized listeners for different concerns\n";
    echo "   • Easy to test telemetry functionality in isolation\n";
    echo "   • Replace or enhance telemetry without changing core code\n\n";

    echo "🔧 Available Events:\n";
    echo "   • OperationStartedEvent - When an operation begins\n";
    echo "   • OperationCompletedEvent - When an operation finishes\n";
    echo "   • HttpRequestSentEvent - When HTTP requests are sent\n";
    echo "   • HttpResponseReceivedEvent - When HTTP responses arrive\n\n";

    echo "💡 Production Setup:\n";
    echo "   • Register listeners through dependency injection\n";
    echo "   • Use PSR-3 logger for structured logging\n";
    echo "   • Export metrics to monitoring systems\n";
    echo "   • Set up alerting based on error rates\n";
} catch (Throwable $e) {
    echo '❌ Error: ' . $e->getMessage() . "\n";
    echo '💡 Make sure OpenFGA is running on http://localhost:8080' . "\n";
    echo '   You can start it with: docker run -p 8080:8080 openfga/openfga run' . "\n";

    $exitCode = 1;
} finally {
    // Clean up the store regardless of success or failure
    if (null !== $storeId && null !== $client) {
        try {
            echo "\n🧹 Cleaning up...\n";
            $client->deleteStore(store: $storeId);
            echo "✅ Store deleted successfully\n";
        } catch (Throwable $cleanupError) {
            echo '⚠️  Failed to delete store: ' . $cleanupError->getMessage() . "\n";
        }
    }

    exit($exitCode);
}
