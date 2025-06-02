# ListTupleChangesRequestInterface

Interface for listing historical changes to relationship tuples. This interface defines the contract for requests that query the change history of relationship tuples within an OpenFGA store. It provides a chronological audit trail of all tuple modifications, including writes and deletes, allowing you to track how relationships have evolved over time. Tuple change history is essential for: - **Auditing**: Track who made changes and when for compliance - **Debugging**: Understand how authorization state reached its current condition - **Synchronization**: Keep external systems in sync with authorization changes - **Analytics**: Analyze access patterns and permission trends over time - **Rollback**: Understand what changes need to be reversed The operation supports: - Time-based filtering to focus on specific periods - Object type filtering to track changes for specific resource types - Pagination for handling large change histories efficiently - Chronological ordering to understand the sequence of changes Each change entry includes the tuple that was modified, the type of operation (write or delete), and the timestamp when the change occurred.

## Namespace
`OpenFGA\Requests`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Requests/ListTupleChangesRequestInterface.php)

## Implements
* [`RequestInterface`](RequestInterface.md)

## Related Classes
* [ListTupleChangesResponseInterface](Responses/ListTupleChangesResponseInterface.md) (response)
* [ListTupleChangesRequest](Requests/ListTupleChangesRequest.md) (implementation)



## Methods

                                                                                    
#### getContinuationToken


```php
public function getContinuationToken(): string|null
```

Get the continuation token for paginated results. Returns the pagination token from a previous list changes operation to continue retrieving results from where the last request left off. This enables efficient pagination through large change histories without missing or duplicating entries.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ListTupleChangesRequestInterface.php#L48)


#### Returns
`string` &#124; `null` — The continuation token from a previous operation, or null for the first page
#### getPageSize


```php
public function getPageSize(): int|null
```

Get the maximum number of changes to return per page. Specifies the page size for paginated results. This controls how many change entries are returned in a single response. Smaller page sizes reduce memory usage and latency, while larger page sizes reduce the number of API calls needed for extensive change histories.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ListTupleChangesRequestInterface.php#L60)


#### Returns
`int` &#124; `null` — The maximum number of changes to return per page, or null to use the default page size
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
#### getStartTime


```php
public function getStartTime(): DateTimeImmutable|null
```

Get the earliest time to include in the change history. Specifies the starting point for the time range of changes to retrieve. Only changes that occurred at or after this time will be included in the results. This allows you to focus on recent changes or specific time periods of interest.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ListTupleChangesRequestInterface.php#L72)


#### Returns
`DateTimeImmutable` &#124; `null` — The earliest timestamp to include in results, or null to include all changes from the beginning
#### getStore


```php
public function getStore(): string
```

Get the store ID containing the tuple changes to list. Identifies which OpenFGA store contains the change history to query. Each store maintains its own independent change log, ensuring complete isolation of audit trails between different authorization domains.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ListTupleChangesRequestInterface.php#L83)


#### Returns
`string` — The store ID containing the tuple change history to retrieve
#### getType


```php
public function getType(): string|null
```

Get the object type filter for changes. Specifies an optional filter to only include changes affecting tuples of a specific object type. This helps narrow the results to changes relevant to particular resource types, such as &quot;document&quot;, &quot;folder&quot;, or &quot;organization&quot;.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ListTupleChangesRequestInterface.php#L95)


#### Returns
`string` &#124; `null` — The object type to filter changes by, or null to include changes for all object types
