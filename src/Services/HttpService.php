<?php

declare(strict_types=1);

namespace OpenFGA\Services;

use InvalidArgumentException;
use JsonException;
use OpenFGA\Events\{EventDispatcherInterface, HttpRequestSentEvent, HttpResponseReceivedEvent};
use OpenFGA\Exceptions\{ClientThrowable, NetworkException};
use OpenFGA\Network\{RequestManager, RequestManagerInterface};
use OpenFGA\Requests\RequestInterface;
use Override;
use Psr\Http\Message\{RequestInterface as HttpRequestInterface, ResponseInterface as HttpResponseInterface};
use Throwable;

/**
 * Default implementation of HttpServiceInterface.
 *
 * This implementation delegates to RequestManager for actual HTTP operations,
 * providing a clean abstraction layer between the Client and network concerns.
 * It emits events for telemetry and observability without direct coupling.
 */
final class HttpService implements HttpServiceInterface
{
    /**
     * The last HTTP request sent.
     */
    private ?HttpRequestInterface $lastRequest = null;

    /**
     * The last HTTP response received.
     */
    private ?HttpResponseInterface $lastResponse = null;

    /**
     * Create a new HttpService instance.
     *
     * @param RequestManagerInterface       $requestManager  The request manager for handling HTTP operations
     * @param EventDispatcherInterface|null $eventDispatcher Optional event dispatcher for telemetry events
     */
    public function __construct(
        private readonly RequestManagerInterface $requestManager,
        private readonly ?EventDispatcherInterface $eventDispatcher = null,
    ) {
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getLastRequest(): ?HttpRequestInterface
    {
        return $this->lastRequest;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getLastResponse(): ?HttpResponseInterface
    {
        return $this->lastResponse;
    }

    /**
     * @inheritDoc
     *
     * @throws ClientThrowable          If request processing fails
     * @throws InvalidArgumentException If request parameters are invalid
     * @throws JsonException            If request serialization fails
     * @throws Throwable                If an unexpected error occurs
     */
    #[Override]
    public function send(RequestInterface $request): HttpResponseInterface
    {
        // Convert OpenFGA request to HTTP request
        $httpRequest = $this->requestManager->request($request);
        $this->lastRequest = $httpRequest;

        // Extract operation context from HTTP request
        $operation = $this->extractOperation($httpRequest);
        $storeId = $this->extractStoreId($httpRequest);
        $modelId = $this->extractModelId($httpRequest);

        // Emit request sent event
        if ($this->eventDispatcher instanceof EventDispatcherInterface) {
            $this->eventDispatcher->dispatch(new HttpRequestSentEvent(
                request: $httpRequest,
                operation: $operation,
                storeId: $storeId,
                modelId: $modelId,
            ));
        }

        try {
            // Send the request and get response
            $httpResponse = $this->requestManager->send($httpRequest);
            $this->lastResponse = $httpResponse;

            // Emit successful response event
            if ($this->eventDispatcher instanceof EventDispatcherInterface) {
                $this->eventDispatcher->dispatch(new HttpResponseReceivedEvent(
                    request: $httpRequest,
                    response: $httpResponse,
                    operation: $operation,
                    storeId: $storeId,
                    modelId: $modelId,
                ));
            }

            return $httpResponse;
        } catch (Throwable $throwable) {
            // Capture the response from NetworkException if available
            if ($throwable instanceof NetworkException && $throwable->response() instanceof HttpResponseInterface) {
                $this->lastResponse = $throwable->response();
            }

            // Emit failed response event
            if ($this->eventDispatcher instanceof EventDispatcherInterface) {
                $this->eventDispatcher->dispatch(new HttpResponseReceivedEvent(
                    request: $httpRequest,
                    response: $this->lastResponse,
                    exception: $throwable,
                    operation: $operation,
                    storeId: $storeId,
                    modelId: $modelId,
                ));
            }

            throw $throwable;
        }
    }

    /**
     * Extract model ID from request URL.
     *
     * @param HttpRequestInterface $request
     */
    private function extractModelId(HttpRequestInterface $request): ?string
    {
        $path = (string) $request->getUri();

        // Extract model ID from URL pattern .../authorization-models/{model_id}/...
        $result = preg_match('/\/authorization-models\/([^\/]+)/', $path, $matches);

        if (1 === $result && isset($matches[1])) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Extract operation name from request URL.
     *
     * @param HttpRequestInterface $request
     */
    private function extractOperation(HttpRequestInterface $request): string
    {
        $path = (string) $request->getUri();

        // Extract operation from URL path
        if (str_contains($path, '/check')) {
            return 'check';
        }

        if (str_contains($path, '/batch-check')) {
            return 'batchCheck';
        }

        if (str_contains($path, '/expand')) {
            return 'expand';
        }

        if (str_contains($path, '/list-objects')) {
            return 'listObjects';
        }

        if (str_contains($path, '/list-users')) {
            return 'listUsers';
        }

        if (str_contains($path, '/read')) {
            return 'readTuples';
        }

        if (str_contains($path, '/write')) {
            return 'writeTuples';
        }

        if (str_contains($path, '/stores')) {
            if (str_contains($path, '/authorization-models')) {
                return str_contains($path, '/assertions') ? 'assertions' : 'authorizationModels';
            }

            return 'stores';
        }

        return 'unknown';
    }

    /**
     * Extract store ID from request URL.
     *
     * @param HttpRequestInterface $request
     */
    private function extractStoreId(HttpRequestInterface $request): ?string
    {
        $path = (string) $request->getUri();

        // Extract store ID from URL pattern /stores/{store_id}/...
        $result = preg_match('/\/stores\/([^\/]+)/', $path, $matches);

        if (1 === $result && isset($matches[1])) {
            return $matches[1];
        }

        return null;
    }
}
