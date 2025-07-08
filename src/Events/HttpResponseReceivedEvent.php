<?php

declare(strict_types=1);

namespace OpenFGA\Events;

use Psr\Http\Message\{RequestInterface, ResponseInterface};
use Throwable;

/**
 * Event fired when an HTTP response is received from the OpenFGA API.
 *
 * This event contains both the request and response for complete telemetry tracking.
 */
final class HttpResponseReceivedEvent extends AbstractEvent
{
    /**
     * Create a new HTTP response received event.
     *
     * @param RequestInterface     $request   The HTTP request that was sent
     * @param ResponseInterface|null $response  The HTTP response received (null if exception occurred)
     * @param Throwable|null       $exception The exception if the request failed
     * @param string               $operation The OpenFGA operation name (for example, 'check', 'write')
     * @param string|null          $storeId   The store ID for the operation
     * @param string|null          $modelId   The model ID for the operation
     */
    public function __construct(
        private readonly RequestInterface $request,
        private readonly ?ResponseInterface $response = null,
        private readonly ?Throwable $exception = null,
        private readonly string $operation = '',
        private readonly ?string $storeId = null,
        private readonly ?string $modelId = null,
    ) {
        parent::__construct([
            'method' => $this->request->getMethod(),
            'uri' => (string) $this->request->getUri(),
            'operation' => $this->operation,
            'store_id' => $this->storeId,
            'model_id' => $this->modelId,
            'status_code' => $this->response?->getStatusCode(),
            'response_size' => $this->response?->getBody()->getSize(),
            'success' => ! $this->exception instanceof Throwable,
            'error_message' => $this->exception?->getMessage(),
        ]);
    }

    /**
     * Get the exception if the request failed.
     *
     * @return Throwable|null The exception or null if the request succeeded
     */
    public function getException(): ?Throwable
    {
        return $this->exception;
    }

    /**
     * Get the model ID for the operation.
     *
     * @return string|null The model ID or null if not applicable
     */
    public function getModelId(): ?string
    {
        return $this->modelId;
    }

    /**
     * Get the OpenFGA operation name.
     *
     * @return string The operation name (for example, 'check', 'write', 'read')
     */
    public function getOperation(): string
    {
        return $this->operation;
    }

    /**
     * Get the HTTP request that was sent.
     *
     * @return RequestInterface The PSR-7 request object
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    /**
     * Get the HTTP response received.
     *
     * @return ResponseInterface|null The PSR-7 response object or null if an exception occurred
     */
    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }

    /**
     * Get the store ID for the operation.
     *
     * @return string|null The store ID or null if not applicable
     */
    public function getStoreId(): ?string
    {
        return $this->storeId;
    }

    /**
     * Check if the HTTP request was successful.
     *
     * @return bool True if no exception occurred, false otherwise
     */
    public function isSuccessful(): bool
    {
        return ! $this->exception instanceof Throwable;
    }
}
