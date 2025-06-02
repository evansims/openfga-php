# ListStoresRequest

Request for listing all available stores with pagination support. This request retrieves a paginated list of stores accessible to the authenticated user or application. It&#039;s useful for store selection interfaces, administrative dashboards, and multi-tenant applications.

## Namespace

`OpenFGA\Requests`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Requests/ListStoresRequest.php)

## Implements

* [`ListStoresRequestInterface`](ListStoresRequestInterface.md)

* [`RequestInterface`](RequestInterface.md)

## Related Classes

* [ListStoresResponse](Responses/ListStoresResponse.md) (response)

* [ListStoresRequestInterface](Requests/ListStoresRequestInterface.md) (interface)

## Methods

#### getContinuationToken

```php
public function getContinuationToken(): ?string

```

Get the continuation token for paginated results. Returns the pagination token from a previous list stores operation to continue retrieving results from where the last request left off. This enables efficient pagination through large numbers of stores without missing or duplicating entries.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ListStoresRequest.php#L52)

#### Returns

`string` &#124; `null` — The continuation token from a previous operation, or null for the first page

#### getPageSize

```php
public function getPageSize(): ?int

```

Get the maximum number of stores to return per page. Specifies the page size for paginated results. This controls how many stores are returned in a single response. Smaller page sizes reduce memory usage and latency, while larger page sizes reduce the number of API calls needed to retrieve all stores.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ListStoresRequest.php#L61)

#### Returns

`int` &#124; `null` — The maximum number of stores to return per page, or null to use the default page size

#### getRequest

```php
public function getRequest(Psr\Http\Message\StreamFactoryInterface $streamFactory): OpenFGA\Network\RequestContext

```

Build a request context for HTTP execution. Transforms the request object into a standardized HTTP request context that can be executed by the OpenFGA HTTP client. This method handles all aspects of request preparation including parameter serialization, URL construction, header configuration, and body stream creation. The method validates that all required parameters are present and properly formatted, serializes complex objects to JSON, constructs the appropriate API endpoint URL, and creates the necessary HTTP message body streams.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ListStoresRequest.php#L70)

#### Parameters

| Name | Type | Description |

|------|------|-------------|

| `$streamFactory` | `StreamFactoryInterface` | PSR-7 stream factory for creating request body streams from serialized data |

#### Returns

[`RequestContext`](Network/RequestContext.md) — The prepared request context containing HTTP method, URL, headers, and body ready for execution
