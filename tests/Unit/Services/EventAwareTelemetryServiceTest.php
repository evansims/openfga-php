<?php

declare(strict_types=1);

use OpenFGA\Events\{EventDispatcherInterface, OperationCompletedEvent, OperationStartedEvent};
use OpenFGA\Models\{AuthorizationModel, Store};
use OpenFGA\Models\Collections\TypeDefinitions;
use OpenFGA\Models\Enums\SchemaVersion;
use OpenFGA\Observability\TelemetryInterface;
use OpenFGA\Services\{EventAwareTelemetryService, TelemetryContext, TelemetryServiceInterface};
use OpenFGA\Tests\Support\Http\{MockRequest, MockResponse};

beforeEach(function (): void {
    $this->mockTelemetry = $this->createMock(TelemetryInterface::class);
    $this->mockEventDispatcher = $this->createMock(EventDispatcherInterface::class);
    $this->service = new EventAwareTelemetryService($this->mockTelemetry, $this->mockEventDispatcher);
    $this->serviceWithoutDispatcher = new EventAwareTelemetryService($this->mockTelemetry);

    $this->store = new Store('store-123', 'Test Store', new DateTimeImmutable, new DateTimeImmutable);
    $this->model = new AuthorizationModel('model-456', SchemaVersion::V1_1, new TypeDefinitions([]));
    $this->request = new MockRequest('GET', '/test');
    $this->response = new MockResponse(200, 100);
    $this->exception = new RuntimeException('Test error');

    $this->context = new TelemetryContext(
        operation: 'test-operation',
        store: $this->store,
        model: $this->model,
        startTime: microtime(true) - 1.0,
        span: (object) ['id' => 'test-span'],
        attributes: ['test' => 'value'],
    );
});

it('implements TelemetryServiceInterface', function (): void {
    expect($this->service)->toBeInstanceOf(TelemetryServiceInterface::class);
});

it('constructs with telemetry and event dispatcher', function (): void {
    expect($this->service)->toBeInstanceOf(EventAwareTelemetryService::class);
});

it('constructs with telemetry only', function (): void {
    expect($this->serviceWithoutDispatcher)->toBeInstanceOf(EventAwareTelemetryService::class);
});

it('records authentication event', function (): void {
    $event = 'token_request';
    $success = true;
    $duration = 0.5;
    $attributes = ['client_id' => 'test-client'];

    $this->mockTelemetry
        ->expects($this->once())
        ->method('recordAuthenticationEvent')
        ->with($event, $success, $duration, $attributes);

    $this->service->recordAuthenticationEvent($event, $success, $duration, $attributes);
});

it('records authentication event without attributes', function (): void {
    $event = 'token_renewal';
    $success = false;
    $duration = 2.0;

    $this->mockTelemetry
        ->expects($this->once())
        ->method('recordAuthenticationEvent')
        ->with($event, $success, $duration, []);

    $this->service->recordAuthenticationEvent($event, $success, $duration);
});

it('records failure with event dispatcher', function (): void {
    $result = ['partial' => 'data'];

    $this->mockTelemetry
        ->expects($this->once())
        ->method('endOperation')
        ->with(
            $this->context->span,
            false,
            $this->exception,
            $this->callback(
                fn ($attributes) => isset($attributes['test'])
                    && 'value' === $attributes['test']
                    && isset($attributes['result_type'], $attributes['error_class'], $attributes['error_message']),
            ),
        );

    $this->mockEventDispatcher
        ->expects($this->once())
        ->method('dispatch')
        ->with($this->callback(fn ($event) => $event instanceof OperationCompletedEvent
                && 'test-operation' === $event->getOperation()
                && false === $event->isSuccessful()
                && $event->getException() === $this->exception
                && 'store-123' === $event->getStoreId()
                && 'model-456' === $event->getModelId()
                && $event->getResult() === $result));

    $this->service->recordFailure($this->context, $this->exception, $result);
});

it('records failure without event dispatcher', function (): void {
    $this->mockTelemetry
        ->expects($this->once())
        ->method('endOperation')
        ->with(
            $this->context->span,
            false,
            $this->exception,
            $this->isType('array'),
        );

    $this->serviceWithoutDispatcher->recordFailure($this->context, $this->exception);
});

it('records failure without result', function (): void {
    $this->mockTelemetry
        ->expects($this->once())
        ->method('endOperation')
        ->with(
            $this->context->span,
            false,
            $this->exception,
            $this->callback(fn ($attributes) => null === $attributes['result_type']),
        );

    $this->mockEventDispatcher
        ->expects($this->once())
        ->method('dispatch')
        ->with($this->callback(fn ($event) => $event instanceof OperationCompletedEvent
                && null === $event->getResult()));

    $this->service->recordFailure($this->context, $this->exception);
});

it('records HTTP request', function (): void {
    $this->mockTelemetry
        ->expects($this->once())
        ->method('startHttpRequest')
        ->with($this->request)
        ->willReturn((object) ['id' => 'http-span']);

    $this->mockTelemetry
        ->expects($this->once())
        ->method('endHttpRequest')
        ->with($this->isInstanceOf('stdClass'), $this->response, $this->exception);

    $this->service->recordHttpRequest($this->request, $this->response, $this->exception);
});

it('records HTTP request with minimal parameters', function (): void {
    $this->mockTelemetry
        ->expects($this->once())
        ->method('startHttpRequest')
        ->with($this->request)
        ->willReturn((object) ['id' => 'http-span']);

    $this->mockTelemetry
        ->expects($this->once())
        ->method('endHttpRequest')
        ->with($this->isInstanceOf('stdClass'), null, null);

    $this->service->recordHttpRequest($this->request);
});

it('records operation metrics', function (): void {
    $operation = 'check';
    $duration = 1.5;
    $attributes = ['user_count' => 100];

    $this->mockTelemetry
        ->expects($this->once())
        ->method('recordOperationMetrics')
        ->with($operation, $duration, $this->store, $this->model, $attributes);

    $this->service->recordOperationMetrics($operation, $duration, $this->store, $this->model, $attributes);
});

it('records operation metrics with string store and model', function (): void {
    $operation = 'write';
    $duration = 0.8;
    $storeId = 'store-string';
    $modelId = 'model-string';

    $this->mockTelemetry
        ->expects($this->once())
        ->method('recordOperationMetrics')
        ->with($operation, $duration, $storeId, $modelId, []);

    $this->service->recordOperationMetrics($operation, $duration, $storeId, $modelId);
});

it('records operation metrics without model and attributes', function (): void {
    $operation = 'list';
    $duration = 0.3;

    $this->mockTelemetry
        ->expects($this->once())
        ->method('recordOperationMetrics')
        ->with($operation, $duration, $this->store, null, []);

    $this->service->recordOperationMetrics($operation, $duration, $this->store);
});

it('records success with event dispatcher', function (): void {
    $result = ['data' => 'success'];

    $this->mockTelemetry
        ->expects($this->once())
        ->method('endOperation')
        ->with(
            $this->context->span,
            true,
            null,
            $this->callback(fn ($attributes) => isset($attributes['test'])
                    && 'value' === $attributes['test']
                    && isset($attributes['result_type'])),
        );

    $this->mockEventDispatcher
        ->expects($this->once())
        ->method('dispatch')
        ->with($this->callback(fn ($event) => $event instanceof OperationCompletedEvent
                && 'test-operation' === $event->getOperation()
                && true === $event->isSuccessful()
                && null === $event->getException()
                && 'store-123' === $event->getStoreId()
                && 'model-456' === $event->getModelId()
                && $event->getResult() === $result));

    $this->service->recordSuccess($this->context, $result);
});

it('records success without event dispatcher', function (): void {
    $this->mockTelemetry
        ->expects($this->once())
        ->method('endOperation')
        ->with(
            $this->context->span,
            true,
            null,
            $this->isType('array'),
        );

    $this->serviceWithoutDispatcher->recordSuccess($this->context);
});

it('records success without result', function (): void {
    $this->mockTelemetry
        ->expects($this->once())
        ->method('endOperation')
        ->with(
            $this->context->span,
            true,
            null,
            $this->callback(fn ($attributes) => null === $attributes['result_type']),
        );

    $this->mockEventDispatcher
        ->expects($this->once())
        ->method('dispatch')
        ->with($this->callback(fn ($event) => $event instanceof OperationCompletedEvent
                && null === $event->getResult()));

    $this->service->recordSuccess($this->context);
});

it('starts operation with event dispatcher', function (): void {
    $operation = 'expand';
    $attributes = ['depth' => 3];

    $this->mockTelemetry
        ->expects($this->once())
        ->method('startOperation')
        ->with($operation, $this->store, $this->model, $attributes)
        ->willReturn((object) ['id' => 'new-span']);

    $this->mockEventDispatcher
        ->expects($this->once())
        ->method('dispatch')
        ->with($this->callback(fn ($event) => $event instanceof OperationStartedEvent
                && $event->getOperation() === $operation
                && 'store-123' === $event->getStoreId()
                && 'model-456' === $event->getModelId()
                && $event->getContext() === $attributes));

    $context = $this->service->startOperation($operation, $this->store, $this->model, $attributes);

    expect($context)->toBeInstanceOf(TelemetryContext::class);
    expect($context->operation)->toBe($operation);
    expect($context->span->id)->toBe('new-span');
    expect($context->store)->toBe($this->store);
    expect($context->model)->toBe($this->model);
    expect($context->attributes)->toBe($attributes);
});

it('starts operation without event dispatcher', function (): void {
    $operation = 'read';

    $this->mockTelemetry
        ->expects($this->once())
        ->method('startOperation')
        ->with($operation, $this->store, null, [])
        ->willReturn((object) ['id' => 'new-span']);

    $context = $this->serviceWithoutDispatcher->startOperation($operation, $this->store);

    expect($context)->toBeInstanceOf(TelemetryContext::class);
    expect($context->operation)->toBe($operation);
});

it('starts operation with string store and model', function (): void {
    $operation = 'check';
    $storeId = 'string-store';
    $modelId = 'string-model';

    $this->mockTelemetry
        ->expects($this->once())
        ->method('startOperation')
        ->with($operation, $storeId, $modelId, [])
        ->willReturn((object) ['id' => 'string-span']);

    $this->mockEventDispatcher
        ->expects($this->once())
        ->method('dispatch')
        ->with($this->callback(fn ($event) => $event instanceof OperationStartedEvent
                && $event->getOperation() === $operation
                && $event->getStoreId() === $storeId
                && $event->getModelId() === $modelId));

    $context = $this->service->startOperation($operation, $storeId, $modelId);

    expect($context->store)->toBe($storeId);
    expect($context->model)->toBe($modelId);
});

it('extracts store ID from store object', function (): void {
    $operation = 'test';

    $this->mockEventDispatcher
        ->expects($this->once())
        ->method('dispatch')
        ->with($this->callback(fn ($event) => 'store-123' === $event->getStoreId()));

    $this->service->startOperation($operation, $this->store);
});

it('extracts store ID from string', function (): void {
    $operation = 'test';
    $storeId = 'direct-store-id';

    $this->mockEventDispatcher
        ->expects($this->once())
        ->method('dispatch')
        ->with($this->callback(fn ($event) => $event->getStoreId() === $storeId));

    $this->service->startOperation($operation, $storeId);
});

it('extracts model ID from model object', function (): void {
    $operation = 'test';

    $this->mockEventDispatcher
        ->expects($this->once())
        ->method('dispatch')
        ->with($this->callback(fn ($event) => 'model-456' === $event->getModelId()));

    $this->service->startOperation($operation, $this->store, $this->model);
});

it('extracts model ID from string', function (): void {
    $operation = 'test';
    $modelId = 'direct-model-id';

    $this->mockEventDispatcher
        ->expects($this->once())
        ->method('dispatch')
        ->with($this->callback(fn ($event) => $event->getModelId() === $modelId));

    $this->service->startOperation($operation, $this->store, $modelId);
});

it('extracts null model ID from null', function (): void {
    $operation = 'test';

    $this->mockEventDispatcher
        ->expects($this->once())
        ->method('dispatch')
        ->with($this->callback(fn ($event) => null === $event->getModelId()));

    $this->service->startOperation($operation, $this->store, null);
});

it('calls telemetry recordOperationMetrics in recordFailure', function (): void {
    $this->mockTelemetry
        ->expects($this->once())
        ->method('recordOperationMetrics')
        ->with(
            'test-operation',
            $this->greaterThan(0.0),
            $this->store,
            $this->model,
            $this->callback(fn ($attributes) => isset($attributes['test'])
                    && 'value' === $attributes['test']
                    && isset($attributes['error'])
                    && true === $attributes['error']
                    && isset($attributes['error_type'])),
        );

    $this->service->recordFailure($this->context, $this->exception);
});

it('calls telemetry recordOperationMetrics in recordSuccess', function (): void {
    $this->mockTelemetry
        ->expects($this->once())
        ->method('recordOperationMetrics')
        ->with(
            'test-operation',
            $this->greaterThan(0.0),
            $this->store,
            $this->model,
            ['test' => 'value'],
        );

    $this->service->recordSuccess($this->context);
});
