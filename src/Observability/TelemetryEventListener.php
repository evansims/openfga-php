<?php

declare(strict_types=1);

namespace OpenFGA\Observability;

use OpenFGA\Events\{HttpRequestSentEvent, HttpResponseReceivedEvent, OperationCompletedEvent, OperationStartedEvent};
use Override;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * Event listener that forwards domain events to the telemetry provider.
 *
 * This decouples business logic from telemetry by using events to communicate
 * what happened without the business logic needing to know about telemetry.
 */
final readonly class TelemetryEventListener implements TelemetryEventListenerInterface
{
    public function __construct(
        private TelemetryInterface $telemetry,
    ) {
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function onHttpRequestSent(HttpRequestSentEvent $event): void
    {
        $request = $event->getRequest();
        $this->telemetry->recordSpan('http.request.sent', [
            'http.method' => $request->getMethod(),
            'http.url' => (string) $request->getUri(),
            'http.request.body.size' => $request->getBody()->getSize(),
            'openfga.operation' => $event->getOperation(),
            'openfga.store_id' => $event->getStoreId(),
            'openfga.model_id' => $event->getModelId(),
        ]);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function onHttpResponseReceived(HttpResponseReceivedEvent $event): void
    {
        $response = $event->getResponse();
        $attributes = [
            'openfga.operation' => $event->getOperation(),
            'openfga.store_id' => $event->getStoreId(),
            'openfga.model_id' => $event->getModelId(),
        ];

        if ($response instanceof ResponseInterface) {
            $attributes['http.status_code'] = $response->getStatusCode();
            $attributes['http.response.body.size'] = $response->getBody()->getSize();
        }

        if ($event->getException() instanceof Throwable) {
            $attributes['exception.type'] = $event->getException()::class;
            $attributes['exception.message'] = $event->getException()->getMessage();
        }

        $this->telemetry->recordSpan('http.response.received', $attributes);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function onOperationCompleted(OperationCompletedEvent $event): void
    {
        $attributes = [
            'openfga.operation' => $event->getOperation(),
            'openfga.operation.success' => $event->isSuccessful(),
            'openfga.store_id' => $event->getStoreId(),
            'openfga.model_id' => $event->getModelId(),
            'openfga.context' => $event->getContext(),
        ];

        if ($event->getException() instanceof Throwable) {
            $attributes['exception.type'] = $event->getException()::class;
            $attributes['exception.message'] = $event->getException()->getMessage();
        }

        $this->telemetry->recordSpan('openfga.operation.completed', $attributes);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function onOperationStarted(OperationStartedEvent $event): void
    {
        $this->telemetry->recordSpan('openfga.operation.started', [
            'openfga.operation' => $event->getOperation(),
            'openfga.store_id' => $event->getStoreId(),
            'openfga.model_id' => $event->getModelId(),
            'openfga.context' => $event->getContext(),
        ]);
    }
}
