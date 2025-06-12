# ListTupleChangesRequest

Request for listing changes to relationship tuples over time. This request retrieves a chronological list of tuple modifications (creates, updates, deletes) within a store. It&#039;s essential for auditing, change tracking, and building event-driven authorization systems that react to permission changes.

## Namespace

`OpenFGA\Requests`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Requests/ListTupleChangesRequest.php)

## Implements

* [`ListTupleChangesRequestInterface`](ListTupleChangesRequestInterface.md)
* [`RequestInterface`](RequestInterface.md)

## Related Classes

* [ListTupleChangesResponse](Responses/ListTupleChangesResponse.md) (response)
* [ListTupleChangesRequestInterface](Requests/ListTupleChangesRequestInterface.md) (interface)

## Methods

#### getContinuationToken

```php
public function getContinuationToken(): ?string

```

Get the continuation token for paginated results. Returns the pagination token from a previous list changes operation to continue retrieving results from where the last request left off. This enables efficient pagination through large change histories without missing or duplicating entries.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ListTupleChangesRequest.php#L64)

#### Returns

`string` &#124; `null` — The continuation token from a previous operation, or null for the first page

#### getPageSize

```php
public function getPageSize(): ?int

```

Get the maximum number of changes to return per page. Specifies the page size for paginated results. This controls how many change entries are returned in a single response. Smaller page sizes reduce memory usage and latency, while larger page sizes reduce the number of API calls needed for extensive change histories.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ListTupleChangesRequest.php#L73)

#### Returns

`int` &#124; `null` — The maximum number of changes to return per page, or null to use the default page size

#### getRequest

```php
public function getRequest(Psr\Http\Message\StreamFactoryInterface $streamFactory): OpenFGA\Network\RequestContext

```

Build a request context for HTTP execution. Transforms the request object into a standardized HTTP request context that can be executed by the OpenFGA HTTP client. This method handles all aspects of request preparation including parameter serialization, URL construction, header configuration, and body stream creation. The method validates that all required parameters are present and properly formatted, serializes complex objects to JSON, constructs the appropriate API endpoint URL, and creates the necessary HTTP message body streams.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ListTupleChangesRequest.php#L82)

#### Parameters

| Name             | Type                     | Description                                                                 |
| ---------------- | ------------------------ | --------------------------------------------------------------------------- |
| `$streamFactory` | `StreamFactoryInterface` | PSR-7 stream factory for creating request body streams from serialized data |

#### Returns

[`RequestContext`](Network/RequestContext.md) — The prepared request context containing HTTP method, URL, headers, and body ready for execution

#### getStartTime

```php
public function getStartTime(): ?DateTimeImmutable

```

Get the earliest time to include in the change history. Specifies the starting point for the time range of changes to retrieve. Only changes that occurred at or after this time will be included in the results. This allows you to focus on recent changes or specific time periods of interest.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ListTupleChangesRequest.php#L103)

#### Returns

`DateTimeImmutable` &#124; `null` — The earliest timestamp to include in results, or null to include all changes from the beginning

#### getStore

```php
public function getStore(): string

```

Get the store ID containing the tuple changes to list. Identifies which OpenFGA store contains the change history to query. Each store maintains its own independent change log, ensuring complete isolation of audit trails between different authorization domains.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ListTupleChangesRequest.php#L112)

#### Returns

`string` — The store ID containing the tuple change history to retrieve

#### getType

```php
public function getType(): ?string

```

Get the object type filter for changes. Specifies an optional filter to only include changes affecting tuples of a specific object type. This helps narrow the results to changes relevant to particular resource types, such as &quot;document&quot;, &quot;folder&quot;, or &quot;organization&quot;.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ListTupleChangesRequest.php#L121)

#### Returns

`string` &#124; `null` — The object type to filter changes by, or null to include changes for all object types
