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

    public function getException(): ?Throwable
    {
        return $this->exception;
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

    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }

    public function getStoreId(): ?string
    {
        return $this->storeId;
    }

    public function isSuccessful(): bool
    {
        return ! $this->exception instanceof Throwable;
    }
}
