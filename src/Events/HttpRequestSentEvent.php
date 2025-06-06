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

    public function getModelId(): ?string
    {
        return $this->modelId;
    }

    public function getOperation(): string
    {
        return $this->operation;
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    public function getStoreId(): ?string
    {
        return $this->storeId;
    }
}
