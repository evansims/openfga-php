# ListStoresRequestInterface

Interface for listing available OpenFGA stores. This interface defines the contract for requests that retrieve a paginated list of all OpenFGA stores accessible to the current authentication context. This is typically used for administrative purposes, allowing users to browse and manage multiple authorization domains. Store listing is essential for: - Administrative dashboards and management interfaces - Store discovery and selection workflows - Monitoring and auditing store usage across an organization - Implementing multi-tenant authorization architectures - Backup and migration tooling that needs to enumerate stores The operation supports pagination to handle large numbers of stores efficiently, ensuring good performance even in environments with hundreds or thousands of authorization domains.

## Namespace
`OpenFGA\Requests`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Requests/ListStoresRequestInterface.php)

## Implements
* [`RequestInterface`](RequestInterface.md)

## Related Classes
* [ListStoresResponseInterface](Responses/ListStoresResponseInterface.md) (response)
* [ListStoresRequest](Requests/ListStoresRequest.md) (implementation)



## Methods

                                                
#### getContinuationToken


```php
public function getContinuationToken(): string|null
```

Get the continuation token for paginated results. Returns the pagination token from a previous list stores operation to continue retrieving results from where the last request left off. This enables efficient pagination through large numbers of stores without missing or duplicating entries.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ListStoresRequestInterface.php#L41)


#### Returns
`string` &#124; `null` — The continuation token from a previous operation, or null for the first page
#### getPageSize


```php
public function getPageSize(): int|null
```

Get the maximum number of stores to return per page. Specifies the page size for paginated results. This controls how many stores are returned in a single response. Smaller page sizes reduce memory usage and latency, while larger page sizes reduce the number of API calls needed to retrieve all stores.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ListStoresRequestInterface.php#L53)


#### Returns
`int` &#124; `null` — The maximum number of stores to return per page, or null to use the default page size
#### getRequest


```php
public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
```

Build a request context for HTTP execution. Transforms the request object into a standardized HTTP request context that can be executed by the OpenFGA HTTP client. This method handles all aspects of request preparation including parameter serialization, URL construction, header configuration, and body stream creation. The method validates that all required parameters are present and properly formatted, serializes complex objects to JSON, constructs the appropriate API endpoint URL, and creates the necessary HTTP message body streams.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/RequestInterface.php#L57)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$streamFactory` | `StreamFactoryInterface` | PSR-7 stream factory for creating request body streams from serialized data |

#### Returns
`RequestContext` — The prepared request context containing HTTP method, URL, headers, and body ready for execution
