# OpenTelemetry Observability with the OpenFGA PHP SDK

The OpenFGA PHP SDK includes comprehensive OpenTelemetry support for observability, providing distributed tracing, metrics collection, and telemetry data to help you monitor, debug, and optimize your authorization workflows. Whether you're troubleshooting performance issues or gaining insights into your application's authorization patterns, the SDK's telemetry features give you the visibility you need.

**New to OpenTelemetry?** It's an open-source observability framework that helps you collect, process, and export telemetry data (metrics, logs, and traces) from your applications. Think of it as a way to understand what your application is doing under the hood.

**Already using OpenTelemetry?** The SDK integrates seamlessly with your existing setup - just configure your telemetry provider and start getting insights into your OpenFGA operations automatically.

## What You'll Get

The SDK automatically instruments and provides telemetry for:

- **HTTP Requests:** All API calls to OpenFGA, including timing, status codes, and errors
- **OpenFGA Operations:** Business-level operations like `check()`, `listObjects()`, `writeTuples()`, etc.
- **Retry Logic:** Failed requests, retry attempts, and backoff delays
- **Circuit Breaker:** State changes and failure rate tracking
- **Authentication:** Token requests, refreshes, and authentication events

## Prerequisites

- **PHP 8.3+** with the OpenFGA PHP SDK installed
- **OpenTelemetry PHP packages** (optional, but recommended for full functionality):
  ```bash
  composer require open-telemetry/api open-telemetry/sdk
  ```
- **An observability backend** like Jaeger, Zipkin, or a cloud service (optional for getting started)

## Quick Start

### 1. Basic Setup (No Backend)

The simplest way to get started is with the built-in telemetry that works without any external dependencies:

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use OpenFGA\Client;
use OpenFGA\Observability\TelemetryFactory;

// Create a telemetry provider
$telemetry = TelemetryFactory::create(
    serviceName: 'my-authorization-service',
    serviceVersion: '1.0.0'
);

// Configure the client with telemetry
$client = new Client(
    url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080',
    telemetry: $telemetry
);

// Your authorization operations are now automatically instrumented!
$result = $client->check(
    store: 'your-store-id',
    model: 'your-model-id',
    tupleKey: tuple(user: 'user:anne', relation: 'viewer', object: 'document:readme')
);
?>
```

### 2. Full OpenTelemetry Setup

For production use with a telemetry backend, install the OpenTelemetry packages and configure them:

```bash
composer require open-telemetry/api open-telemetry/sdk
```

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use OpenFGA\Client;
use OpenFGA\Observability\TelemetryFactory;
use OpenTelemetry\API\Globals;
use OpenTelemetry\API\Trace\Propagation\TraceContextPropagator;
use OpenTelemetry\Contrib\Otlp\SpanExporter;
use OpenTelemetry\SDK\Trace\SpanProcessor\SimpleSpanProcessor;
use OpenTelemetry\SDK\Trace\TracerProvider;

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
    url: $_ENV['FGA_API_URL'],
    telemetry: $telemetry
);

// Operations are now traced and exported to your backend
$result = $client->listObjects(
    store: 'store_123',
    model: 'model_456',
    user: 'user:anne',
    relation: 'viewer',
    type: 'document'
);
?>
```

## Telemetry Data Collected

### HTTP Request Telemetry

Every HTTP request to the OpenFGA API is automatically instrumented:

**Traces (Spans):**

- Span name: `HTTP {METHOD}` (e.g., `HTTP POST`)
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

- Span name: `openfga.{operation}` (e.g., `openfga.check`, `openfga.write_tuples`)
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
use OpenFGA\Observability\TelemetryFactory;
use OpenTelemetry\API\Globals;

// Get your configured tracer and meter
$tracer = Globals::tracerProvider()->getTracer('my-service', '1.0.0');
$meter = Globals::meterProvider()->getMeter('my-service', '1.0.0');

// Create telemetry with custom providers
$telemetry = TelemetryFactory::createWithCustomProviders($tracer, $meter);

$client = new Client(
    url: $_ENV['FGA_API_URL'],
    telemetry: $telemetry
);
```

### No-Op Mode

For testing or when you want to disable telemetry:

```php
use OpenFGA\Observability\TelemetryFactory;

// Explicitly disable telemetry
$telemetry = TelemetryFactory::createNoOp(); // Returns null

$client = new Client(
    url: $_ENV['FGA_API_URL'],
    telemetry: $telemetry
);

// Or simply pass null directly
$client = new Client(
    url: $_ENV['FGA_API_URL'],
    telemetry: null  // No telemetry
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
<?php
use OpenTelemetry\Contrib\Jaeger\Exporter as JaegerExporter;
use OpenTelemetry\SDK\Trace\SpanProcessor\SimpleSpanProcessor;
use OpenTelemetry\SDK\Trace\TracerProvider;

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
?>
```

### Cloud Providers

For cloud-based observability services:

```php
<?php
// AWS X-Ray, Google Cloud Trace, Azure Monitor, etc.
use OpenTelemetry\Contrib\Otlp\SpanExporter;

$exporter = new SpanExporter($_ENV['OTEL_EXPORTER_OTLP_ENDPOINT']);
// Configure with your cloud provider's specific settings
?>
```

### Existing OpenTelemetry Setup

If you already have OpenTelemetry configured in your application:

```php
<?php
// The SDK will automatically use your existing global configuration
$telemetry = TelemetryFactory::create('my-authorization-service');

$client = new Client(
    url: $_ENV['FGA_API_URL'],
    telemetry: $telemetry
);

// Traces will be included in your existing observability setup
?>
```

## Example: Complete Authorization Workflow with Tracing

Here's a complete example showing how telemetry works throughout an authorization workflow:

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use OpenFGA\Client;
use OpenFGA\Observability\TelemetryFactory;
use function OpenFGA\{tuple, tuples};

// Configure telemetry (assumes OpenTelemetry is set up)
$telemetry = TelemetryFactory::create(
    serviceName: 'document-service',
    serviceVersion: '1.0.0'
);

$client = new Client(
    url: $_ENV['FGA_API_URL'],
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
?>
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

- **`OperationStartedEvent`** - When an OpenFGA operation begins (check, write, etc.)
- **`OperationCompletedEvent`** - When an operation finishes (success or failure)
- **`HttpRequestSentEvent`** - When HTTP requests are sent to the OpenFGA API
- **`HttpResponseReceivedEvent`** - When HTTP responses are received

### Creating Custom Event Listeners

Here's how to create and register custom event listeners:

```php
<?php

use OpenFGA\Events\{EventDispatcher, HttpRequestSentEvent, HttpResponseReceivedEvent, OperationCompletedEvent, OperationStartedEvent};

// Create a logging listener
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
<?php

require_once __DIR__ . '/vendor/autoload.php';

use OpenFGA\Client;
use OpenFGA\Events\EventDispatcher;

use function OpenFGA\{allowed, dsl, model, store, tuple, write};

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
    url: 'http://localhost:8080',
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
}
```

**Security Monitoring:**

```php
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
            ]);
        }
    }
}
```

**Performance Analytics:**

```php
final class PerformanceEventListener
{
    private array $operationTimings = [];

    public function onOperationCompleted(OperationCompletedEvent $event): void
    {
        $timing = $this->calculateTiming($event);

        // Export to your analytics platform
        $this->exportToAnalytics([
            'operation' => $event->getOperation(),
            'duration_ms' => $timing,
            'store_id' => $event->getStoreId(),
            'success' => $event->isSuccessful(),
        ]);
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
    // ... more listeners

    return $dispatcher;
});

// Configure the client to use the dispatcher
$container->singleton(Client::class, function ($container) {
    return new Client(
        url: $_ENV['FGA_API_URL'],
        eventDispatcher: $container->get(EventDispatcher::class),
    );
});
```

## Advanced Usage

### Custom Attributes

Add custom context to your authorization operations:

```php
// The SDK automatically includes relevant attributes, but you can add more context
// when configuring your service or through OpenTelemetry's context propagation

use OpenTelemetry\API\Trace\Span;

// Add custom attributes to the current span
$span = Span::getCurrent();
$span->setAttribute('user.department', 'engineering');
$span->setAttribute('request.source', 'mobile-app');

// Now perform your authorization check
$result = $client->check(/* ... */);
```

### Correlation with Application Traces

The SDK integrates with your application's existing traces:

```php
// If you have an existing span (e.g., from a web request)
$parentSpan = $yourFramework->getCurrentSpan();

// OpenFGA operations will automatically become child spans
$result = $client->check(/* ... */); // This becomes a child of $parentSpan
```

### Metrics-Only Mode

If you only want metrics without distributed tracing:

```php
// Configure OpenTelemetry with metrics only
use OpenTelemetry\SDK\Metrics\MeterProvider;

$meterProvider = new MeterProvider(/* your exporters */);
// Don't configure a tracer provider

$telemetry = TelemetryFactory::create('my-service');
```

## Next Steps

**Getting Started:**

- Try the basic setup with your existing OpenFGA instance
- Add Jaeger for local development to see traces immediately
- Review the [Introduction.md](Introduction.md) guide for basic OpenFGA usage
- Run the [event-driven telemetry example](../examples/event-driven-telemetry/example.php) to see custom listeners in action

**Production Setup:**

- Configure proper sampling rates for high-traffic applications
- Set up dashboards in your observability platform
- Implement alerting on key metrics like error rates and latency
- Consider event-driven telemetry for custom monitoring integrations

**Integration:**

- Explore the [Authentication.md](Authentication.md) guide for secure telemetry
- Read about [Results.md](Results.md) for error handling patterns that work well with observability
- Check [Queries.md](Queries.md) for the operations you'll be monitoring

**Examples:**

- [OpenTelemetry observability example](../examples/observability/example.php) - Complete OpenTelemetry setup
- [Event-driven telemetry example](../examples/event-driven-telemetry/example.php) - Custom event listeners
- [All observability examples](../examples/README.md) - Complete collection

For more details on the OpenTelemetry ecosystem, visit the [official OpenTelemetry documentation](https://opentelemetry.io/docs/).
