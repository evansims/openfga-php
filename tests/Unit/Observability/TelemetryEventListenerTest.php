<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Observability;

use OpenFGA\Events\{HttpRequestSentEvent, OperationCompletedEvent, OperationStartedEvent};
use OpenFGA\Observability\TelemetryEventListener;
use OpenFGA\Tests\Support\Http\MockRequest;
use OpenFGA\Tests\Support\Observability\MockTelemetry;
use stdClass;

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
