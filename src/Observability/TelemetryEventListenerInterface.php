<?php

declare(strict_types=1);

namespace OpenFGA\Observability;

use OpenFGA\Events\{HttpRequestSentEvent, HttpResponseReceivedEvent, OperationCompletedEvent, OperationStartedEvent};

/**
 * Interface for event listeners that forward domain events to telemetry providers.
 *
 * This interface defines the contract for handling telemetry-related events
 * throughout the OpenFGA client lifecycle, enabling observability without
 * tightly coupling business logic to telemetry concerns.
 */
interface TelemetryEventListenerInterface
{
    /**
     * Handle HTTP request sent events.
     *
     * Records telemetry data when an HTTP request is sent, including request
     * method, URL, body size, and OpenFGA-specific context like operation,
     * store ID, and model ID.
     *
     * @param HttpRequestSentEvent $event The HTTP request sent event
     */
    public function onHttpRequestSent(HttpRequestSentEvent $event): void;

    /**
     * Handle HTTP response received events.
     *
     * Records telemetry data when an HTTP response is received, including
     * response status, body size, and any exception information if the
     * request failed.
     *
     * @param HttpResponseReceivedEvent $event The HTTP response received event
     */
    public function onHttpResponseReceived(HttpResponseReceivedEvent $event): void;

    /**
     * Handle operation completed events.
     *
     * Records telemetry data when an OpenFGA operation completes, including
     * success status, operation context, and exception details if the
     * operation failed.
     *
     * @param OperationCompletedEvent $event The operation completed event
     */
    public function onOperationCompleted(OperationCompletedEvent $event): void;

    /**
     * Handle operation started events.
     *
     * Records telemetry data when an OpenFGA operation begins, including
     * operation type, store context, and model information.
     *
     * @param OperationStartedEvent $event The operation started event
     */
    public function onOperationStarted(OperationStartedEvent $event): void;
}
