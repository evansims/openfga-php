<?php

declare(strict_types=1);

namespace OpenFGA\Events;

use Psr\Http\Message\RequestInterface;

/**
 * Event fired when an HTTP request is sent to the OpenFGA API.
 *
 * This event contains the outgoing request details for telemetry and debugging.
 */
final class HttpRequestSentEvent extends AbstractEvent
{
    /**
     * Create a new HTTP request sent event.
     *
     * @param RequestInterface $request   The HTTP request being sent
     * @param string           $operation The OpenFGA operation name (for example, 'check', 'write')
     * @param string|null      $storeId   The store ID for the operation
     * @param string|null      $modelId   The model ID for the operation
     */
    public function __construct(
        private readonly RequestInterface $request,
        private readonly string $operation,
        private readonly ?string $storeId = null,
        private readonly ?string $modelId = null,
    ) {
        parent::__construct([
            'method' => $this->request->getMethod(),
            'uri' => (string) $this->request->getUri(),
            'operation' => $this->operation,
            'store_id' => $this->storeId,
            'model_id' => $this->modelId,
            'headers' => $this->request->getHeaders(),
            'body_size' => $this->request->getBody()->getSize(),
        ]);
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
     * Get the HTTP request being sent.
     *
     * @return RequestInterface The PSR-7 request object
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
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
}
