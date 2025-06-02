# RequestManager

Concrete implementation of HTTP request management for OpenFGA API communication. This class provides the core HTTP communication layer for the OpenFGA SDK, handling all aspects of request construction, execution, and response processing. It integrates with PSR-7 HTTP message interfaces and PSR-18 HTTP clients to provide a flexible, testable HTTP transport layer. The RequestManager manages: - PSR-17 factory auto-discovery and configuration - HTTP client configuration and request execution - Authentication header management - Request URL construction and routing - Error response parsing and exception handling - User-Agent header management for SDK identification The implementation uses lazy initialization for PSR components, automatically discovering suitable implementations when not explicitly provided. This ensures compatibility with a wide range of HTTP libraries while maintaining optimal performance.

## Namespace

`OpenFGA\Network`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Network/RequestManager.php)

## Implements

* [`RequestManagerInterface`](RequestManagerInterface.md)

## Related Classes

* [RequestManagerInterface](Network/RequestManagerInterface.md) (interface)

## Methods

### List Operations

#### getHttpClient

```php
public function getHttpClient(): Psr\Http\Client\ClientInterface

```

Get the configured PSR-18 HTTP client. Returns the HTTP client instance used for executing requests to the OpenFGA API. The client handles the actual network communication and can be any PSR-18 compatible implementation such as Guzzle, cURL, or others. If no client was explicitly provided during construction, the RequestManager will attempt to discover one automatically using PSR Discovery. The HTTP client is responsible for network-level concerns including connection management, SSL/TLS handling, timeout enforcement, and low-level HTTP protocol implementation.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Network/RequestManager.php#L133)

#### Returns

`ClientInterface` — The PSR-18 HTTP client instance for executing requests

#### getHttpRequestFactory

```php
public function getHttpRequestFactory(): Psr\Http\Message\RequestFactoryInterface

```

Get the configured PSR-17 HTTP request factory. Returns the factory used for creating PSR-7 HTTP request objects. This factory is used to construct HTTP requests from OpenFGA request contexts, including setting the appropriate method, URI, headers, and body content. If no factory was explicitly provided during construction, the RequestManager will attempt to discover one automatically using PSR Discovery.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Network/RequestManager.php#L155)

#### Returns

`RequestFactoryInterface` — The PSR-17 factory for creating HTTP request objects

#### getHttpResponseFactory

```php
public function getHttpResponseFactory(): Psr\Http\Message\ResponseFactoryInterface

```

Get the configured PSR-17 HTTP response factory. Returns the factory used for creating PSR-7 HTTP response objects. This is primarily used for testing and mocking scenarios where custom responses need to be constructed programmatically. If no factory was explicitly provided during construction, the RequestManager will attempt to discover one automatically using PSR Discovery.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Network/RequestManager.php#L177)

#### Returns

`ResponseFactoryInterface` — The PSR-17 factory for creating HTTP response objects

#### getHttpStreamFactory

```php
public function getHttpStreamFactory(): Psr\Http\Message\StreamFactoryInterface

```

Get the configured PSR-17 HTTP stream factory. Returns the factory used for creating PSR-7 stream objects for HTTP message bodies. This factory is used to convert request data (such as JSON payloads) into stream objects that can be attached to HTTP requests. If no factory was explicitly provided during construction, the RequestManager will attempt to discover one automatically using PSR Discovery.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Network/RequestManager.php#L199)

#### Returns

`StreamFactoryInterface` — The PSR-17 factory for creating HTTP message body streams

### Other

#### handleResponseException

*<small>Implements Network\RequestManagerInterface</small>*

```php
public function handleResponseException(ResponseInterface $response, HttpRequestInterface|null $request = NULL): never

```

Handle error responses by throwing appropriate exceptions. Analyzes HTTP error responses to determine the specific type of error and throws the most appropriate exception with comprehensive context information. This method processes: - HTTP status codes (400, 401, 403, 404, 409, 422, 500, etc.) - Error response bodies containing detailed error information - OpenFGA-specific error codes and messages - Request context for debugging purposes The method provides structured error information that applications can use for error handling, user messaging, and debugging. Different exception types are thrown based on the error category to enable appropriate handling strategies. Common error scenarios include: - 400 Bad Request: Invalid request parameters or malformed data - 401 Unauthorized: Missing or invalid authentication credentials - 403 Forbidden: Valid credentials but insufficient permissions - 404 Not Found: Requested resource (store, model) does not exist - 409 Conflict: Request conflicts with current resource state - 422 Unprocessable Entity: Request timeout or processing limits exceeded - 500 Internal Server Error: Server-side processing failures

[View source](https://github.com/evansims/openfga-php/blob/main/src/Network/RequestManagerInterface.php#L77)

#### Parameters

| Name | Type | Description |

|------|------|-------------|

| `$response` | [`ResponseInterface`](Responses/ResponseInterface.md) | The HTTP error response to analyze and convert to an exception |

| `$request` | `HttpRequestInterface` &#124; `null` | Optional request context for enhanced error reporting |

#### Returns

`never` — This method always throws an exception and never returns normally

#### request

```php
public function request(OpenFGA\Requests\RequestInterface $request): Psr\Http\Message\RequestInterface

```

Convert an OpenFGA request into a PSR-7 HTTP request. Transforms high-level OpenFGA API requests into standardized PSR-7 HTTP requests that can be executed by any PSR-18 compliant HTTP client. This process includes: - Building the complete request URL from the base API URL and endpoint path - Setting appropriate HTTP method based on the operation type - Adding authentication headers using configured credentials - Serializing request data to JSON and creating appropriate body streams - Setting required headers (Content-Type, User-Agent, etc.) The conversion process ensures that all OpenFGA API requirements are met, including proper content negotiation, authentication, and request formatting according to the OpenFGA API specification.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Network/RequestManager.php#L220)

#### Parameters

| Name | Type | Description |

|------|------|-------------|

| `$request` | [`RequestInterface`](Requests/RequestInterface.md) | The high-level OpenFGA API request to convert |

#### Returns

`Psr\Http\Message\RequestInterface` — The PSR-7 HTTP request ready for execution

#### send

```php
public function send(Psr\Http\Message\RequestInterface $request): Psr\Http\Message\ResponseInterface

```

Send an HTTP request and return the response. Executes the provided PSR-7 HTTP request using the configured HTTP client with comprehensive error handling and retry logic. This method handles: - Network-level errors (connection failures, timeouts, DNS issues) - HTTP-level errors (4xx and 5xx status codes) - Automatic retry logic for transient failures - Response validation and error context extraction The method provides detailed error information for debugging, including request/response details, error codes, and suggested remediation steps when requests fail. Successful responses are returned as-is for further processing by the calling code.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Network/RequestManager.php#L263)

#### Parameters

| Name | Type | Description |

|------|------|-------------|

| `$request` | `Psr\Http\Message\RequestInterface` | The PSR-7 HTTP request to execute |

#### Returns

`Psr\Http\Message\ResponseInterface` — The HTTP response from the OpenFGA API
