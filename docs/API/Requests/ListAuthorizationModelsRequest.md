# ListAuthorizationModelsRequest

Request for listing all authorization models in a store. This request retrieves a paginated list of authorization models, including their IDs and metadata. It&#039;s useful for browsing available models, model management interfaces, and selecting models for operations.

## Namespace
`OpenFGA\Requests`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Requests/ListAuthorizationModelsRequest.php)

## Implements
* [`ListAuthorizationModelsRequestInterface`](ListAuthorizationModelsRequestInterface.md)
* [`RequestInterface`](RequestInterface.md)

## Related Classes
* [ListAuthorizationModelsResponse](Responses/ListAuthorizationModelsResponse.md) (response)
* [ListAuthorizationModelsRequestInterface](Requests/ListAuthorizationModelsRequestInterface.md) (interface)

## Methods

#### getContinuationToken

```php
public function getContinuationToken(): ?string
```

Get the continuation token for paginated results. Returns the pagination token from a previous list models operation to continue retrieving results from where the last request left off. This enables efficient pagination through stores with many model versions without missing or duplicating entries.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ListAuthorizationModelsRequest.php#L54)

#### Returns
`string` &#124; `null` — The continuation token from a previous operation, or null for the first page
#### getPageSize

```php
public function getPageSize(): ?int
```

Get the maximum number of models to return per page. Specifies the page size for paginated results. This controls how many authorization models are returned in a single response. Smaller page sizes reduce memory usage and latency, while larger page sizes reduce the number of API calls needed to retrieve all model versions.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ListAuthorizationModelsRequest.php#L63)

#### Returns
`int` &#124; `null` — The maximum number of models to return per page, or null to use the default page size
#### getRequest

```php
public function getRequest(Psr\Http\Message\StreamFactoryInterface $streamFactory): OpenFGA\Network\RequestContext
```

Build a request context for HTTP execution. Transforms the request object into a standardized HTTP request context that can be executed by the OpenFGA HTTP client. This method handles all aspects of request preparation including parameter serialization, URL construction, header configuration, and body stream creation. The method validates that all required parameters are present and properly formatted, serializes complex objects to JSON, constructs the appropriate API endpoint URL, and creates the necessary HTTP message body streams.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ListAuthorizationModelsRequest.php#L72)

#### Parameters
| Name             | Type                     | Description                                                                 |
| ---------------- | ------------------------ | --------------------------------------------------------------------------- |
| `$streamFactory` | `StreamFactoryInterface` | PSR-7 stream factory for creating request body streams from serialized data |

#### Returns
[`RequestContext`](Network/RequestContext.md) — The prepared request context containing HTTP method, URL, headers, and body ready for execution
#### getStore

```php
public function getStore(): string
```

Get the store ID containing the authorization models to list. Identifies which OpenFGA store contains the authorization models to enumerate. Each store maintains its own independent collection of model versions, representing the evolution of that store&#039;s authorization schema over time.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ListAuthorizationModelsRequest.php#L91)

#### Returns
`string` — The store ID containing the authorization models to list
