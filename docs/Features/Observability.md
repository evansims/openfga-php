The OpenFGA PHP SDK includes comprehensive OpenTelemetry support for observability, providing distributed tracing, metrics collection, and telemetry data to help you monitor, debug, and optimize your authorization workflows. Whether you're troubleshooting performance issues or gaining insights into your application's authorization patterns, the SDK's telemetry features give you the visibility you need.

**New to OpenTelemetry?** It's an open-source observability framework that helps you collect, process, and export telemetry data (metrics, logs, and traces) from your applications. Think of it as a way to understand what your application is doing under the hood.

**Already using OpenTelemetry?** The SDK integrates seamlessly with your existing setup - just configure your telemetry provider and start getting insights into your OpenFGA operations automatically.

## Table of Contents

- What You Get
- Prerequisites
- Quick Start
- Telemetry Data Collected
- Configuration Options
- Common Integration Patterns
- Example: Complete Authorization Workflow with Tracing
- Viewing Your Telemetry Data
- Troubleshooting
- Event-Driven Telemetry
- Advanced OpenTelemetry Integration
- Advanced Monitoring Patterns
- Testing Advanced Observability

## What You Get

The SDK automatically instruments and provides telemetry for:

- **HTTP Requests:** All API calls to OpenFGA, including timing, status codes, and errors
- **OpenFGA Operations:** Business-level operations like `check()`, `listObjects()`, `writeTuples()`, etc.
- **Retry Logic:** Failed requests, retry attempts, and backoff delays
- **Circuit Breaker:** State changes and failure rate tracking
- **Authentication:** Token requests, refreshes, and authentication events

## Prerequisites

All examples in this guide assume the following setup:

**Requirements:**

- **PHP 8.3+** with the OpenFGA PHP SDK installed
- **OpenTelemetry PHP packages** (optional, but recommended for full functionality):

  ```bash
  composer require open-telemetry/api open-telemetry/sdk
  ```

- **An OpenTelemetry processing/exporting setup.** While not strictly required to enable telemetry in the SDK, you'll need a way to process and view your telemetry data. This can range from a simple console exporter for local development, a local Jaeger/Zipkin instance, to a full cloud-based observability service.

**Common imports and setup code:**

```php
require_once __DIR__ . '/vendor/autoload.php';

// OpenFGA SDK imports
use OpenFGA\Client;
use OpenFGA\Observability\TelemetryFactory;

// OpenTelemetry imports (when using full OpenTelemetry setup)
use OpenTelemetry\API\Globals;
use OpenTelemetry\API\Trace\Propagation\TraceContextPropagator;
use OpenTelemetry\API\Trace\Span;
use OpenTelemetry\Contrib\Otlp\SpanExporter;
use OpenTelemetry\SDK\Trace\SpanProcessor\SimpleSpanProcessor;
use OpenTelemetry\SDK\Trace\TracerProvider;

// Event-driven telemetry imports
use OpenFGA\Events\{
    EventDispatcher,
    HttpRequestSentEvent,
    HttpResponseReceivedEvent,
    OperationCompletedEvent,
    OperationStartedEvent
};

// Helper functions for common operations
use function OpenFGA\{allowed, dsl, model, store, tuple, tuples, write};

// Basic client configuration (customize for your environment)
$apiUrl = $_ENV['FGA_API_URL'] ?? 'http://localhost:8080';
$storeId = 'your-store-id';
$modelId = 'your-model-id';
```

## Quick Start

For production use with a telemetry backend, install the OpenTelemetry packages and configure them:

```bash
composer require open-telemetry/api open-telemetry/sdk
```

```php
// Configure OpenTelemetry (this is a basic example)
$tracerProvider = new TracerProvider([
    new SimpleSpanProcessor(
        new SpanExporter($_ENV['OTEL_EXPORTER_OTLP_ENDPOINT'] ?? 'http://localhost:4317')
    )
]);

Globals::registerInitializer(function () use ($tracerProvider) {
    return \OpenTelemetry\SDK\Registry::get()->tracerProvider($tracerProvider);
});

// Create telemetry with your service information
$telemetry = TelemetryFactory::create(
    serviceName: 'my-authorization-service',
    serviceVersion: '1.2.3'
);

// Configure client
$client = new Client(
    url: $apiUrl,
    telemetry: $telemetry
);

// Operations are now traced and exported to your backend
$result = $client->listObjects(
    store: $storeId,
    model: $modelId,
    user: 'user:anne',
    relation: 'viewer',
    type: 'document'
);
```

## Telemetry Data Collected

### HTTP Request Telemetry

Every HTTP request to the OpenFGA API is automatically instrumented:

**Traces (Spans):**

- Span name: `HTTP {METHOD}` (for example `HTTP POST`)
- Duration of the entire HTTP request/response cycle
- HTTP method, URL, status code, response size
- Error details if the request fails

**Metrics:**

- `openfga.http.requests.total` - Counter of HTTP requests by method, status code, and success/failure

**Example span attributes:**

```
http.method: POST
http.url: https://api.fga.example/stores/123/check
http.scheme: https
http.host: api.fga.example
http.status_code: 200
http.response.size: 1024
openfga.sdk.name: openfga-php
openfga.sdk.version: 1.0.0
```

### OpenFGA Operation Telemetry

Business-level operations provide higher-level observability:

**Traces (Spans):**

- Span name: `openfga.{operation}` (for example `openfga.check`, `openfga.write_tuples`)
- Duration of the business operation (may include multiple HTTP calls)
- Store ID, model ID, and operation-specific metadata

**Metrics:**

- `openfga.operations.total` - Counter of operations by type, store, success/failure
- `openfga.operations.duration` - Histogram of operation durations

**Example operation span:**

```
openfga.operation: check
openfga.store_id: store_01H1234567890ABCDEF
openfga.model_id: model_01H1234567890ABCDEF
openfga.sdk.name: openfga-php
openfga.sdk.version: 1.0.0
```

### Retry and Reliability Telemetry

The SDK automatically tracks retry attempts and circuit breaker behavior:

**Retry Metrics:**

- `openfga.retries.total` - Counter of retry attempts by endpoint and outcome
- `openfga.retries.delay` - Histogram of retry delays in milliseconds

**Circuit Breaker Metrics:**

- `openfga.circuit_breaker.state_changes.total` - Counter of state changes (open/closed)

**Authentication Telemetry:**

- `openfga.auth.events.total` - Counter of authentication events
- `openfga.auth.duration` - Histogram of authentication operation durations

## Configuration Options

### Service Identification

Configure your service information for better observability:

```php
$telemetry = TelemetryFactory::create(
    serviceName: 'user-management-api',     // Your service name
    serviceVersion: '2.1.0'                 // Your service version
);
```

### Custom Telemetry Providers

You can provide your own configured OpenTelemetry tracer and meter:

```php
// Get your configured tracer and meter
$tracer = Globals::tracerProvider()->getTracer('my-service', '1.0.0');
$meter = Globals::meterProvider()->getMeter('my-service', '1.0.0');

// Create telemetry with custom providers
$telemetry = TelemetryFactory::createWithCustomProviders($tracer, $meter);

$client = new Client(
    url: $apiUrl,
    telemetry: $telemetry
);
```

## Common Integration Patterns

### Jaeger (Local Development)

For local development with Jaeger:

```bash
# Start Jaeger with Docker
docker run -d --name jaeger \
  -p 16686:16686 \
  -p 14250:14250 \
  jaegertracing/all-in-one:latest
```

```php
use OpenTelemetry\Contrib\Jaeger\Exporter as JaegerExporter;

$tracerProvider = new TracerProvider([
    new SimpleSpanProcessor(
        new JaegerExporter(
            'my-service',
            'http://localhost:14268/api/traces'
        )
    )
]);

Globals::registerInitializer(function () use ($tracerProvider) {
    return \OpenTelemetry\SDK\Registry::get()->tracerProvider($tracerProvider);
});

$telemetry = TelemetryFactory::create('my-service', '1.0.0');
```

### Cloud Providers

For cloud-based observability services:

```php
// AWS X-Ray, Google Cloud Trace, Azure Monitor, etc.
$exporter = new SpanExporter($_ENV['OTEL_EXPORTER_OTLP_ENDPOINT']);
// Configure with your cloud provider's specific settings
```

### Existing OpenTelemetry Setup

If you already have OpenTelemetry configured in your application:

```php
// The SDK will automatically use your existing global configuration
$telemetry = TelemetryFactory::create('my-authorization-service');

$client = new Client(
    url: $apiUrl,
    telemetry: $telemetry
);

// Traces will be included in your existing observability setup
```

## Example: Complete Authorization Workflow with Tracing

Here's a complete example showing how telemetry works throughout an authorization workflow:

```php
// Configure telemetry (assumes OpenTelemetry is set up)
$telemetry = TelemetryFactory::create(
    serviceName: 'document-service',
    serviceVersion: '1.0.0'
);

$client = new Client(
    url: $apiUrl,
    telemetry: $telemetry
);

try {
    // Each operation creates its own span with timing and metadata

    // 1. Create store - traced as "openfga.create_store"
    $store = $client->createStore(name: 'document-service-store')
        ->unwrap();

    // 2. Create model - traced as "openfga.create_authorization_model"
    $model = $client->createAuthorizationModel(
        store: $store->getId(),
        typeDefinitions: $authModel->getTypeDefinitions()
    )->unwrap();

    // 3. Write relationships - traced as "openfga.write_tuples"
    $client->writeTuples(
        store: $store->getId(),
        model: $model->getId(),
        writes: tuples(
            tuple(user: 'user:anne', relation: 'viewer', object: 'document:readme'),
            tuple(user: 'user:bob', relation: 'editor', object: 'document:readme')
        )
    )->unwrap();

    // 4. Check authorization - traced as "openfga.check"
    $allowed = $client->check(
        store: $store->getId(),
        model: $model->getId(),
        tupleKey: tuple(user: 'user:anne', relation: 'viewer', object: 'document:readme')
    )->unwrap();

    // 5. List accessible objects - traced as "openfga.list_objects"
    $documents = $client->listObjects(
        store: $store->getId(),
        model: $model->getId(),
        user: 'user:anne',
        relation: 'viewer',
        type: 'document'
    )->unwrap();

    echo "Authorization check complete. Anne can view document: " .
         ($allowed->getAllowed() ? 'Yes' : 'No') . "\n";
    echo "Documents Anne can view: " . count($documents->getObjects()) . "\n";

} catch (Throwable $e) {
    // Errors are automatically recorded in spans
    echo "Authorization failed: " . $e->getMessage() . "\n";
}
```

## Viewing Your Telemetry Data

### In Jaeger UI

1. Open http://localhost:16686 in your browser
2. Select your service name from the dropdown
3. Click "Find Traces" to see recent authorization operations
4. Click on a trace to see the detailed span timeline

### Key Things to Look For

**Performance Analysis:**

- Which operations take the longest?
- Are there patterns in slow requests?
- How do retry attempts affect overall timing?

**Error Investigation:**

- What HTTP status codes are you getting?
- Which OpenFGA operations are failing?
- Are authentication issues causing problems?

**Usage Patterns:**

- Which stores and models are accessed most frequently?
- What types of authorization checks are most common?
- How often do retries occur?

## Troubleshooting

### No Telemetry Data

1. **Check if OpenTelemetry is properly installed:**

   ```bash
   composer show | grep open-telemetry
   ```

2. **Verify your exporter configuration:**

   ```php
   // Add debug output
   $telemetry = TelemetryFactory::create('test-service');
   if ($telemetry instanceof \OpenFGA\Observability\OpenTelemetryProvider) {
       echo "Using OpenTelemetry provider\n";
   } elseif ($telemetry === null) {
       echo "No telemetry configured\n";
   }
   ```

3. **Check your backend connectivity:**

   - Ensure your OTLP endpoint is reachable
   - Verify authentication if required
   - Check firewall and network settings

### Performance Impact

The telemetry overhead is minimal in production:

- **No-op mode:** Virtually zero overhead when telemetry is disabled
- **OpenTelemetry mode:** Low overhead (~1-2% typically) with async exporters
- **Graceful degradation:** Continues working even if telemetry backend is unavailable

### Environment Variables

Common OpenTelemetry environment variables that work with the SDK:

```bash
# Service identification
export OTEL_SERVICE_NAME="my-authorization-service"
export OTEL_SERVICE_VERSION="1.0.0"

# Exporter configuration
export OTEL_EXPORTER_OTLP_ENDPOINT="http://localhost:4317"
export OTEL_EXPORTER_OTLP_HEADERS="api-key=your-api-key"

# Sampling (to reduce overhead in high-traffic scenarios)
export OTEL_TRACES_SAMPLER="traceidratio"
export OTEL_TRACES_SAMPLER_ARG="0.1"  # Sample 10% of traces
```

## Event-Driven Telemetry

The SDK provides a powerful event-driven telemetry system that allows you to create custom observability solutions without tight coupling to the main client functionality. This approach lets you build specialized listeners for different concerns like logging, metrics collection, alerting, or custom analytics.

### Available Events

The SDK emits events at key points during operation execution:

- **`OperationStartedEvent`** **OperationStartedEvent** - When an OpenFGA operation begins (check, write, etc.)
- **`OperationCompletedEvent`** **OperationCompletedEvent** - When an operation finishes (success or failure)
- **`HttpRequestSentEvent`** **HttpRequestSentEvent** - When HTTP requests are sent to the OpenFGA API
- **`HttpResponseReceivedEvent`** **HttpResponseReceivedEvent** - When HTTP responses are received

### Creating Custom Event Listeners

Here's how to create and register custom event listeners:

```php
// Create a logging listener
// Note: This is an example helper class and not part of the SDK.
final class LoggingEventListener
{
    public function onHttpRequestSent(HttpRequestSentEvent $event): void
    {
        echo "[{$event->getOperation()}] HTTP Request: {$event->getRequest()->getMethod()} {$event->getRequest()->getUri()}\n";
    }

    public function onHttpResponseReceived(HttpResponseReceivedEvent $event): void
    {
        $status = $event->getResponse() ? $event->getResponse()->getStatusCode() : 'N/A';
        $success = $event->isSuccessful() ? '✅' : '❌';
        echo "[{$event->getOperation()}] HTTP Response: {$success} {$status}\n";
    }

    public function onOperationStarted(OperationStartedEvent $event): void
    {
        echo "[{$event->getOperation()}] Started - Store: {$event->getStoreId()}\n";
    }

    public function onOperationCompleted(OperationCompletedEvent $event): void
    {
        $success = $event->isSuccessful() ? '✅' : '❌';
        echo "[{$event->getOperation()}] Completed: {$success}\n";
    }
}

// Create a metrics listener
// Note: This is an example helper class and not part of the SDK.
final class MetricsEventListener
{
    private array $operationTimes = [];
    private array $requestCounts = [];

    public function onOperationStarted(OperationStartedEvent $event): void
    {
        $this->operationTimes[$event->getEventId()] = microtime(true);
    }

    public function onOperationCompleted(OperationCompletedEvent $event): void
    {
        $operation = $event->getOperation();

        // Count operations
        $this->requestCounts[$operation] = ($this->requestCounts[$operation] ?? 0) + 1;

        // Track timing
        if (isset($this->operationTimes[$event->getEventId()])) {
            $duration = microtime(true) - $this->operationTimes[$event->getEventId()];
            echo "[{$operation}] completed in " . round($duration * 1000, 2) . "ms\n";
            unset($this->operationTimes[$event->getEventId()]);
        }
    }

    public function getMetrics(): array
    {
        return [
            'request_counts' => $this->requestCounts,
            'active_operations' => count($this->operationTimes),
        ];
    }
}
```

### Registering Event Listeners

Register your listeners with the event dispatcher:

```php
// Create event dispatcher and listeners
$eventDispatcher = new EventDispatcher();
$loggingListener = new LoggingEventListener();
$metricsListener = new MetricsEventListener();

// Register listeners for different events
$eventDispatcher->addListener(HttpRequestSentEvent::class, [$loggingListener, 'onHttpRequestSent']);
$eventDispatcher->addListener(HttpResponseReceivedEvent::class, [$loggingListener, 'onHttpResponseReceived']);
$eventDispatcher->addListener(OperationStartedEvent::class, [$loggingListener, 'onOperationStarted']);
$eventDispatcher->addListener(OperationCompletedEvent::class, [$loggingListener, 'onOperationCompleted']);

// Register metrics listener
$eventDispatcher->addListener(OperationStartedEvent::class, [$metricsListener, 'onOperationStarted']);
$eventDispatcher->addListener(OperationCompletedEvent::class, [$metricsListener, 'onOperationCompleted']);

// Note: In production, you would configure the event dispatcher through dependency injection
// The above example shows the concept for educational purposes
```

### Complete Event-Driven Example

Here's a complete example showing event-driven telemetry in action:

```php
// Your custom listeners (defined above)
$eventDispatcher = new EventDispatcher();
$loggingListener = new LoggingEventListener();
$metricsListener = new MetricsEventListener();

// Register all listeners
$eventDispatcher->addListener(HttpRequestSentEvent::class, [$loggingListener, 'onHttpRequestSent']);
$eventDispatcher->addListener(HttpResponseReceivedEvent::class, [$loggingListener, 'onHttpResponseReceived']);
$eventDispatcher->addListener(OperationStartedEvent::class, [$loggingListener, 'onOperationStarted']);
$eventDispatcher->addListener(OperationCompletedEvent::class, [$loggingListener, 'onOperationCompleted']);
$eventDispatcher->addListener(OperationStartedEvent::class, [$metricsListener, 'onOperationStarted']);
$eventDispatcher->addListener(OperationCompletedEvent::class, [$metricsListener, 'onOperationCompleted']);

$client = new Client(
    url: $apiUrl,
    eventDispatcher: $eventDispatcher,
);

// Perform operations - events will be triggered automatically
$storeId = store($client, 'telemetry-demo');

$authModel = dsl($client, '
    model
      schema 1.1
    type user
    type document
      relations
        define viewer: [user]
');
$modelId = model($client, $storeId, $authModel);

write($client, $storeId, $modelId, tuple('user:alice', 'viewer', 'document:report'));
$canView = allowed($client, $storeId, $modelId, tuple('user:alice', 'viewer', 'document:report'));

// View collected metrics
echo "Collected Metrics:\n";
print_r($metricsListener->getMetrics());
```

### Production Use Cases

**Custom Alerting:**

```php
// Note: This is an example helper class and not part of the SDK.
final class AlertingEventListener
{
    public function onOperationCompleted(OperationCompletedEvent $event): void
    {
        if (!$event->isSuccessful()) {
            // Send alert to your monitoring system
            $this->sendAlert([
                'operation' => $event->getOperation(),
                'store_id' => $event->getStoreId(),
                'error' => $event->getException()?->getMessage(),
            ]);
        }
    }

    private function sendAlert(array $data): void
    {
        // Integration with your alerting system
        // Example: PagerDuty, Slack, email, etc.
        $alertPayload = json_encode([
            'severity' => 'warning',
            'summary' => "OpenFGA operation failed: {$data['operation']}",
            'details' => $data,
            'timestamp' => date('c'),
        ]);

        // Send to your alerting endpoint
        // curl_post($alertingEndpoint, $alertPayload);
    }
}
```

**Security Monitoring:**

```php
// Note: This is an example helper class and not part of the SDK.
final class SecurityEventListener
{
    public function onOperationStarted(OperationStartedEvent $event): void
    {
        if ($event->getOperation() === 'check') {
            // Log authorization attempts for security analysis
            $this->logSecurityEvent([
                'timestamp' => time(),
                'operation' => $event->getOperation(),
                'store_id' => $event->getStoreId(),
                'user_ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            ]);
        }
    }

    private function logSecurityEvent(array $event): void
    {
        // Send to security information and event management (SIEM) system
        $securityLog = json_encode([
            'event_type' => 'authorization_check',
            'metadata' => $event,
        ]);

        // Log to security monitoring system
        error_log($securityLog, 3, '/var/log/security/openfga.log');
    }
}
```

**Performance Analytics:**

```php
// Note: This is an example helper class and not part of the SDK.
final class PerformanceEventListener
{
    private array $operationTimings = [];

    public function onOperationStarted(OperationStartedEvent $event): void
    {
        $this->operationTimings[$event->getEventId()] = microtime(true);
    }

    public function onOperationCompleted(OperationCompletedEvent $event): void
    {
        $timing = $this->calculateTiming($event);

        // Export to your analytics platform
        $this->exportToAnalytics([
            'operation' => $event->getOperation(),
            'duration_ms' => $timing,
            'store_id' => $event->getStoreId(),
            'success' => $event->isSuccessful(),
            'timestamp' => time(),
        ]);
    }

    private function calculateTiming(OperationCompletedEvent $event): float
    {
        $startTime = $this->operationTimings[$event->getEventId()] ?? microtime(true);
        return (microtime(true) - $startTime) * 1000; // Convert to milliseconds
    }

    private function exportToAnalytics(array $data): void
    {
        // Send to analytics platform (Google Analytics, Mixpanel, etc.)
        $analyticsPayload = json_encode([
            'event_name' => 'openfga_operation',
            'properties' => $data,
        ]);

        // Send to analytics endpoint
        // $this->analyticsClient->track($analyticsPayload);
    }
}
```

### Benefits of Event-Driven Telemetry

- **Decoupling:** Observability logic is separate from business logic
- **Flexibility:** Add multiple listeners for the same events
- **Specialization:** Create focused listeners for different concerns
- **Testability:** Easy to unit test telemetry functionality in isolation
- **Extensibility:** Add new observability features without changing core code
- **Custom Integration:** Perfect for integrating with proprietary monitoring systems

### Integration with Dependency Injection

In production applications, register listeners through your DI container:

```php
// In your service provider or DI configuration
$container->singleton(EventDispatcher::class, function () {
    $dispatcher = new EventDispatcher();

    // Register all your listeners
    $dispatcher->addListener(OperationStartedEvent::class, [LoggingEventListener::class, 'onOperationStarted']);
    $dispatcher->addListener(OperationCompletedEvent::class, [MetricsEventListener::class, 'onOperationCompleted']);
    $dispatcher->addListener(OperationCompletedEvent::class, [AlertingEventListener::class, 'onOperationCompleted']);
    $dispatcher->addListener(OperationStartedEvent::class, [SecurityEventListener::class, 'onOperationStarted']);
    // ... more listeners

    return $dispatcher;
});

// Configure the client to use the dispatcher
$container->singleton(Client::class, function ($container) {
    return new Client(
        url: $apiUrl,
        eventDispatcher: $container->get(EventDispatcher::class),
    );
});
```

## Advanced OpenTelemetry Integration

### Custom Attributes

Add custom context to your authorization operations:

```php
// The SDK automatically includes relevant attributes, but you can add more context
// when configuring your service or through OpenTelemetry's context propagation

// Add custom attributes to the current span
$span = Span::getCurrent();
$span->setAttribute('user.department', 'engineering');
$span->setAttribute('request.source', 'mobile-app');
$span->setAttribute('tenant.id', $tenantId);
$span->setAttribute('session.id', $sessionId);

// Now perform your authorization check
$result = $client->check(
    store: $storeId,
    model: $modelId,
    tupleKey: tuple(user: 'user:anne', relation: 'viewer', object: 'document:readme')
);
```

### Correlation with Application Traces

The SDK integrates with your application's existing traces:

```php
// If you have an existing span (for example from a web request)
$parentSpan = $yourFramework->getCurrentSpan();

// OpenFGA operations will automatically become child spans
$result = $client->check(
    store: $storeId,
    model: $modelId,
    tupleKey: tuple(user: 'user:anne', relation: 'viewer', object: 'document:readme')
); // This becomes a child of $parentSpan
```

### Distributed Tracing Across Services

When your authorization spans multiple services:

```php
// Service A: Create a span and propagate context
$tracer = Globals::tracerProvider()->getTracer('user-service');
$span = $tracer->spanBuilder('authorize_user_access')->startSpan();

// Inject trace context into headers for service-to-service calls
$headers = [];
TraceContextPropagator::getInstance()->inject($headers);

// Service B: Extract context and continue the trace
$extractedContext = TraceContextPropagator::getInstance()->extract($headers);
Context::storage()->attach($extractedContext);

// OpenFGA operations will continue the distributed trace
$allowed = $client->check(
    store: $storeId,
    model: $modelId,
    tupleKey: tuple(user: 'user:anne', relation: 'viewer', object: 'document:readme')
);
```

### Metrics-Only Mode

If you only want metrics without distributed tracing:

```php
// Configure OpenTelemetry with metrics only
use OpenTelemetry\SDK\Metrics\MeterProvider;
use OpenTelemetry\Contrib\Otlp\MetricExporter;

$meterProvider = new MeterProvider([
    new MetricExporter($_ENV['OTEL_EXPORTER_OTLP_ENDPOINT'])
]);

// Don't configure a tracer provider - only metrics will be collected
$telemetry = TelemetryFactory::create('my-service');
```

### Custom Sampling Strategies

For high-traffic applications, implement custom sampling:

```php
// Custom sampler based on operation type
use OpenTelemetry\SDK\Trace\Sampler\ParentBased;
use OpenTelemetry\SDK\Trace\Sampler\TraceIdRatioBasedSampler;

$customSampler = new ParentBased(
    new TraceIdRatioBasedSampler(0.1) // Sample 10% of traces
);

$tracerProvider = new TracerProvider([
    new SimpleSpanProcessor($exporter)
], sampler: $customSampler);
```

## Advanced Monitoring Patterns

### Circuit Breaker Monitoring

Monitor and alert on circuit breaker state changes:

```php
// Note: This is an example helper class and not part of the SDK.
final class CircuitBreakerEventListener
{
    public function onOperationCompleted(OperationCompletedEvent $event): void
    {
        // Check if the failure rate indicates circuit breaker activation
        $failureRate = $this->calculateFailureRate($event->getStoreId());

        if ($failureRate > 0.5) { // 50% failure threshold
            $this->alertCircuitBreakerRisk([
                'store_id' => $event->getStoreId(),
                'failure_rate' => $failureRate,
                'operation' => $event->getOperation(),
            ]);
        }
    }

    private function calculateFailureRate(string $storeId): float
    {
        // Calculate failure rate for the store over recent operations
        // This would integrate with your metrics storage
        return 0.0; // Placeholder
    }
}
```

### A/B Testing Integration

Track authorization model performance across different versions:

```php
// Note: This is an example helper class and not part of the SDK.
final class ABTestingEventListener
{
    public function onOperationCompleted(OperationCompletedEvent $event): void
    {
        $this->trackModelPerformance([
            'model_id' => $event->getModelId(),
            'operation' => $event->getOperation(),
            'duration_ms' => $event->getDuration(),
            'success' => $event->isSuccessful(),
            'variant' => $this->getModelVariant($event->getModelId()),
        ]);
    }

    private function getModelVariant(string $modelId): string
    {
        // Determine which A/B test variant this model represents
        return 'control'; // or 'treatment'
    }
}
```

### SLA Monitoring

Track service level objectives:

```php
// Note: This is an example helper class and not part of the SDK.
final class SLAEventListener
{
    private const SLO_LATENCY_MS = 100; // 100ms SLO
    private const SLO_SUCCESS_RATE = 0.999; // 99.9% success rate

    public function onOperationCompleted(OperationCompletedEvent $event): void
    {
        $this->recordSLAMetrics([
            'operation' => $event->getOperation(),
            'latency_slo_met' => $event->getDuration() <= self::SLO_LATENCY_MS,
            'success_slo_met' => $event->isSuccessful(),
            'timestamp' => time(),
        ]);
    }

    private function recordSLAMetrics(array $metrics): void
    {
        // Send to SLA monitoring dashboard
        // Track error budget consumption
    }
}
```

## Testing Advanced Observability

### Unit Testing Event Listeners

```php
use PHPUnit\Framework\TestCase;

class SecurityEventListenerTest extends TestCase
{
    public function testLogsSecurityEventForCheckOperations(): void
    {
        $listener = new SecurityEventListener();
        $event = new OperationStartedEvent(
            eventId: 'test-123',
            operation: 'check',
            storeId: 'store-123'
        );

        // Capture log output
        ob_start();
        $listener->onOperationStarted($event);
        $output = ob_get_clean();

        $this->assertStringContainsString('authorization_check', $output);
        $this->assertStringContainsString('store-123', $output);
    }
}
```

### Integration Testing with Telemetry

```php
class TelemetryIntegrationTest extends TestCase
{
    public function testOperationCreatesExpectedSpans(): void
    {
        // Configure test tracer
        $spanProcessor = new InMemorySpanProcessor();
        $tracerProvider = new TracerProvider([$spanProcessor]);

        $telemetry = TelemetryFactory::createWithCustomProviders(
            $tracerProvider->getTracer('test'),
            null
        );

        $client = new Client(
            url: 'http://localhost:8080',
            telemetry: $telemetry
        );

        // Perform operation
        $client->check(
            store: 'test-store',
            model: 'test-model',
            tupleKey: tuple('user:test', 'viewer', 'doc:test')
        );

        // Assert spans were created
        $spans = $spanProcessor->getSpans();
        $this->assertCount(2, $spans); // HTTP + operation spans
        $this->assertEquals('openfga.check', $spans[1]->getName());
    }
}
```
