<?php

declare(strict_types=1);

namespace OpenFGA\Network;

use Override;
use Psr\Http\Message\StreamInterface;

/**
 * Implementation of request context for OpenFGA API operations.
 *
 * This class provides a concrete implementation of the RequestContextInterface,
 * encapsulating all the information needed to construct and execute HTTP requests
 * to the OpenFGA API. It stores request metadata including HTTP method, URL,
 * headers, body content, and routing configuration in an immutable structure.
 *
 * The RequestContext serves as a data transfer object that carries request
 * information from the high-level API operations down to the HTTP transport
 * layer, ensuring that all necessary context is preserved throughout the
 * request processing pipeline.
 *
 * @see RequestContextInterface Request context interface
 */
final readonly class RequestContext implements RequestContextInterface
{
    /**
     * Create a new request context for an OpenFGA API operation.
     *
     * Constructs an immutable request context containing all the information
     * necessary to execute an HTTP request to the OpenFGA API. The context
     * encapsulates the request method, target URL, optional body content,
     * custom headers, and routing configuration.
     *
     * @param RequestMethod         $method    The HTTP method for this request (GET, POST, PUT, DELETE)
     * @param string                $url       The target URL path for the API operation, typically relative to the base API URL
     * @param StreamInterface|null  $body      Optional request body stream containing JSON data for POST/PUT operations
     * @param array<string, string> $headers   Custom HTTP headers to include with the request, merged with default headers
     * @param bool                  $useApiUrl Whether to prepend the base API URL to the request URL (true for most operations)
     */
    public function __construct(
        private RequestMethod $method,
        private string $url,
        private ?StreamInterface $body = null,
        private array $headers = [],
        private bool $useApiUrl = true,
    ) {
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getBody(): ?StreamInterface
    {
        return $this->body;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getMethod(): RequestMethod
    {
        return $this->method;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function useApiUrl(): bool
    {
        return $this->useApiUrl;
    }
}
