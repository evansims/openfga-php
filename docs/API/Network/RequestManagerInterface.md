# RequestManagerInterface

Manages HTTP requests and responses for OpenFGA API communication. This interface defines the core HTTP communication layer for the OpenFGA SDK, responsible for translating high-level API operations into HTTP requests and processing the responses. It handles all aspects of HTTP communication including request construction, authentication, error handling, and response processing. The RequestManager serves as the bridge between the OpenFGA client&#039;s domain-specific operations and the underlying HTTP transport layer. It abstracts away the complexities of HTTP communication while providing a clean, testable interface for API interactions. Key responsibilities include: - Converting OpenFGA requests to PSR-7 HTTP requests - Managing HTTP client configuration and PSR component integration - Handling authentication headers and API credentials - Executing HTTP requests with retry logic and timeout management - Processing HTTP responses and converting errors to appropriate exceptions - Providing comprehensive error context for debugging and monitoring The implementation uses PSR-7 HTTP message interfaces for maximum compatibility with existing PHP HTTP ecosystems and follows PSR-18 HTTP client standards for pluggable HTTP transport implementations.

## Namespace
`OpenFGA\Network`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Network/RequestManagerInterface.php)

## Related Classes
* [RequestManager](Network/RequestManager.md) (implementation)

## Methods

### List Operations
#### getHttpClient

```php
public function getHttpClient(): ClientInterface
```

Get the configured PSR-18 HTTP client. Returns the HTTP client instance used for executing requests to the OpenFGA API. The client handles the actual network communication and can be any PSR-18 compatible implementation such as Guzzle, cURL, or others. If no client was explicitly provided during construction, the RequestManager will attempt to discover one automatically using PSR Discovery. The HTTP client is responsible for network-level concerns including connection management, SSL/TLS handling, timeout enforcement, and low-level HTTP protocol implementation.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Network/RequestManagerInterface.php#L96)

#### Returns
[`ClientInterface`](ClientInterface.md) — The PSR-18 HTTP client instance for executing requests
#### getHttpRequestFactory

```php
public function getHttpRequestFactory(): RequestFactoryInterface
```

Get the configured PSR-17 HTTP request factory. Returns the factory used for creating PSR-7 HTTP request objects. This factory is used to construct HTTP requests from OpenFGA request contexts, including setting the appropriate method, URI, headers, and body content. If no factory was explicitly provided during construction, the RequestManager will attempt to discover one automatically using PSR Discovery.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Network/RequestManagerInterface.php#L112)

#### Returns
`RequestFactoryInterface` — The PSR-17 factory for creating HTTP request objects
#### getHttpResponseFactory

```php
public function getHttpResponseFactory(): ResponseFactoryInterface
```

Get the configured PSR-17 HTTP response factory. Returns the factory used for creating PSR-7 HTTP response objects. This is primarily used for testing and mocking scenarios where custom responses need to be constructed programmatically. If no factory was explicitly provided during construction, the RequestManager will attempt to discover one automatically using PSR Discovery.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Network/RequestManagerInterface.php#L128)

#### Returns
`ResponseFactoryInterface` — The PSR-17 factory for creating HTTP response objects
#### getHttpStreamFactory

```php
public function getHttpStreamFactory(): StreamFactoryInterface
```

Get the configured PSR-17 HTTP stream factory. Returns the factory used for creating PSR-7 stream objects for HTTP message bodies. This factory is used to convert request data (such as JSON payloads) into stream objects that can be attached to HTTP requests. If no factory was explicitly provided during construction, the RequestManager will attempt to discover one automatically using PSR Discovery.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Network/RequestManagerInterface.php#L144)

#### Returns
`StreamFactoryInterface` — The PSR-17 factory for creating HTTP message body streams
### Other
#### request

```php
public function request(RequestInterface $request): HttpRequestInterface
```

Convert an OpenFGA request into a PSR-7 HTTP request. Transforms high-level OpenFGA API requests into standardized PSR-7 HTTP requests that can be executed by any PSR-18 compliant HTTP client. This process includes: - Building the complete request URL from the base API URL and endpoint path - Setting appropriate HTTP method based on the operation type - Adding authentication headers using configured credentials - Serializing request data to JSON and creating appropriate body streams - Setting required headers (Content-Type, User-Agent, etc.) The conversion process ensures that all OpenFGA API requirements are met, including proper content negotiation, authentication, and request formatting according to the OpenFGA API specification.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Network/RequestManagerInterface.php#L169)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$request` | [`RequestInterface`](Requests/RequestInterface.md) | The high-level OpenFGA API request to convert |

#### Returns
`HttpRequestInterface` — The PSR-7 HTTP request ready for execution
#### send

```php
public function send(HttpRequestInterface $request): ResponseInterface
```

Send an HTTP request and return the response. Executes the provided PSR-7 HTTP request using the configured HTTP client with comprehensive error handling and retry logic. This method handles: - Network-level errors (connection failures, timeouts, DNS issues) - HTTP-level errors (4xx and 5xx status codes) - Automatic retry logic for transient failures - Response validation and error context extraction The method provides detailed error information for debugging, including request/response details, error codes, and suggested remediation steps when requests fail. Successful responses are returned as-is for further processing by the calling code.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Network/RequestManagerInterface.php#L192)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$request` | `HttpRequestInterface` | The PSR-7 HTTP request to execute |

#### Returns
[`ResponseInterface`](Responses/ResponseInterface.md) — The HTTP response from the OpenFGA API
