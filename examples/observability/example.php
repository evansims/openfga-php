<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use Buzz\Client\FileGetContents;
use Nyholm\Psr7\Factory\Psr17Factory;
use OpenFGA\Client;
use OpenFGA\Observability\TelemetryFactory;

use function OpenFGA\{allowed, dsl, model, store, tuple, tuples, write};

/*
 * OpenTelemetry Observability Example
 *
 * This example demonstrates how to integrate OpenTelemetry observability
 * into your application using the OpenFGA PHP SDK. The SDK automatically
 * instruments HTTP requests, authorization checks, and operations when
 * OpenTelemetry is properly configured.
 */

try {
    echo "🔭 OpenFGA OpenTelemetry Observability Example\n\n";

    // =============================================================================
    // Example 1: Production Setup with OpenTelemetry
    // =============================================================================

    echo "📊 Setting up OpenFGA client with OpenTelemetry instrumentation...\n";

    // In production, configure your OpenTelemetry SDK first:
    // - Set OTEL_EXPORTER_OTLP_ENDPOINT environment variable
    // - Configure your service name and version
    // - Set up your tracing backend (Jaeger, Zipkin, etc.)

    $client = new Client(
        url: 'http://localhost:8080',
        telemetry: TelemetryFactory::create(
            serviceName: 'my-auth-service',
            serviceVersion: '1.2.0',
        ),
        httpClient: new FileGetContents(new Psr17Factory),
        httpResponseFactory: new Psr17Factory,
        httpStreamFactory: new Psr17Factory,
        httpRequestFactory: new Psr17Factory,
    );

    echo "✅ Client configured with telemetry support\n\n";

    // =============================================================================
    // Example 2: Instrumented Authorization Operations
    // =============================================================================

    echo "🔍 Performing instrumented authorization operations...\n";

    // Create a store - this will be traced with span: "openfga.create_store"
    echo "Creating store...\n";
    $storeId = store($client, 'ecommerce-demo');
    echo "✅ Store created: {$storeId}\n";

    // Define authorization model - traced with span: "openfga.create_authorization_model"
    echo "Creating authorization model...\n";
    $modelDsl = '
        model
          schema 1.1

        type user

        type document
          relations
            define reader: [user]
            define writer: [user]
            define owner: [user]
    ';

    $authModel = dsl($client, $modelDsl);
    $modelId = model($client, $storeId, $authModel);

    echo "✅ Authorization model created: {$modelId}\n";

    // Write relationship tuples - traced with span: "openfga.write_tuples"
    echo "Writing relationship tuples...\n";
    write(
        client: $client,
        store: $storeId,
        model: $authModel,
        tuples: tuples(
            tuple('user:alice', 'owner', 'document:budget'),
            tuple('user:bob', 'reader', 'document:budget'),
        ),
    );
    echo "✅ Relationship tuples written\n";

    // Check authorization - traced with span: "openfga.check"
    // Each check will include timing, success/failure, and contextual metadata
    echo "Checking permissions...\n";

    $canAliceRead = allowed(
        client: $client,
        store: $storeId,
        model: $modelId,
        tuple: tuple('user:alice', 'reader', 'document:budget'),
    );

    $canBobWrite = allowed(
        client: $client,
        store: $storeId,
        model: $modelId,
        tuple: tuple('user:bob', 'writer', 'document:budget'),
    );

    echo '✅ Alice can read budget: ' . ($canAliceRead ? 'YES' : 'NO') . "\n";
    echo '✅ Bob can write budget: ' . ($canBobWrite ? 'YES' : 'NO') . "\n";

    echo "\n";

    // =============================================================================
    // What Gets Instrumented?
    // =============================================================================

    echo "📋 What gets automatically instrumented:\n";
    echo "   • HTTP requests to OpenFGA API (timing, status codes, errors)\n";
    echo "   • Authorization checks with tuple details\n";
    echo "   • Store and model operations\n";
    echo "   • Relationship tuple writes and reads\n";
    echo "   • Error conditions and retry attempts\n";
    echo "   • Circuit breaker state changes\n\n";

    echo "🎯 Observability Benefits:\n";
    echo "   • Track authorization performance across your application\n";
    echo "   • Monitor API latency and error rates\n";
    echo "   • Debug authorization failures with detailed traces\n";
    echo "   • Correlate authorization decisions with business metrics\n";
    echo "   • Set up alerts for authorization system health\n\n";

    echo "🔧 Production Setup Tips:\n";
    echo "   • Set OTEL_SERVICE_NAME environment variable\n";
    echo "   • Configure OTEL_EXPORTER_OTLP_ENDPOINT for your backend\n";
    echo "   • Use sampling to control trace volume in high-traffic apps\n";
    echo "   • Add custom attributes to traces for better filtering\n";
    echo "   • Monitor both authorization latency and business KPIs\n\n";

    echo "✨ Example completed! Check your OpenTelemetry backend for traces.\n";
} catch (Throwable $e) {
    echo '❌ Error: ' . $e->getMessage() . "\n";
    echo "💡 Make sure OpenFGA is running on http://localhost:8080\n";
    echo "   You can start it with: docker run -p 8080:8080 openfga/openfga run\n";

    exit(1);
}
