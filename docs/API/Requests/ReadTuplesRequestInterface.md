# ReadTuplesRequestInterface

Interface for reading relationship tuples from an OpenFGA store. This interface defines the contract for requests that query relationship tuples stored in OpenFGA. It supports filtering by tuple patterns and provides pagination for handling large result sets efficiently. Read operations allow you to: - Query existing relationships using tuple key patterns - Filter by specific objects, users, or relations - Use wildcard patterns to match multiple tuples - Configure read consistency for performance optimization - Paginate through large result sets with continuation tokens This is essential for auditing permissions, syncing data to external systems, building administrative interfaces, and implementing custom authorization logic that needs to inspect the relationship graph.

<details>
<summary><strong>Table of Contents</strong></summary>

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Related Classes](#related-classes)
- [Methods](#methods)

- [`getConsistency()`](#getconsistency)
  - [`getContinuationToken()`](#getcontinuationtoken)
  - [`getPageSize()`](#getpagesize)
  - [`getRequest()`](#getrequest)
  - [`getStore()`](#getstore)
  - [`getTupleKey()`](#gettuplekey)

</details>

## Namespace

`OpenFGA\Requests`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Requests/ReadTuplesRequestInterface.php)

## Implements

- [`RequestInterface`](RequestInterface.md)

## Related Classes

- [ReadTuplesResponseInterface](Responses/ReadTuplesResponseInterface.md) (response)
- [ReadTuplesRequest](Requests/ReadTuplesRequest.md) (implementation)

## Methods

### getConsistency

```php
public function getConsistency(): Consistency|null

```

Get the read consistency level for the read operation. Determines the consistency guarantees for reading relationship tuples. This allows you to balance between read performance and data freshness based on your application&#039;s requirements.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ReadTuplesRequestInterface.php#L43)

#### Returns

[`Consistency`](Models/Enums/Consistency.md) &#124; `null` — The consistency level for the operation, or null to use the default consistency setting

### getContinuationToken

```php
public function getContinuationToken(): string|null

```

Get the continuation token for paginated results. Returns the pagination token from a previous read operation to continue retrieving results from where the last request left off. This enables efficient pagination through large result sets without missing or duplicating tuples.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ReadTuplesRequestInterface.php#L55)

#### Returns

`string` &#124; `null` — The continuation token from a previous read operation, or null for the first page

### getPageSize

```php
public function getPageSize(): int|null

```

Get the maximum number of tuples to return. Specifies the page size for paginated results. This controls how many relationship tuples are returned in a single response. Smaller page sizes reduce memory usage and latency, while larger page sizes reduce the number of API calls needed for large datasets.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ReadTuplesRequestInterface.php#L67)

#### Returns

`int` &#124; `null` — The maximum number of tuples to return per page, or null to use the default page size

### getRequest

```php
public function getRequest(StreamFactoryInterface $streamFactory): RequestContext

```

Build a request context for HTTP execution. Transforms the request object into a standardized HTTP request context that can be executed by the OpenFGA HTTP client. This method handles all aspects of request preparation including parameter serialization, URL construction, header configuration, and body stream creation. The method validates that all required parameters are present and properly formatted, serializes complex objects to JSON, constructs the appropriate API endpoint URL, and creates the necessary HTTP message body streams.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/RequestInterface.php#L57)

#### Parameters

| Name             | Type                     | Description                                                                 |
| ---------------- | ------------------------ | --------------------------------------------------------------------------- |
| `$streamFactory` | `StreamFactoryInterface` | PSR-7 stream factory for creating request body streams from serialized data |

#### Returns

`RequestContext` — The prepared request context containing HTTP method, URL, headers, and body ready for execution

### getStore

```php
public function getStore(): string

```

Get the store ID containing the tuples to read. Identifies which OpenFGA store contains the relationship tuples to query. All read operations will be performed within the context of this specific store, ensuring data isolation from other stores.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ReadTuplesRequestInterface.php#L78)

#### Returns

`string` — The store ID containing the relationship tuples to read

### getTupleKey

```php
public function getTupleKey(): TupleKeyInterface

```

Get the tuple key pattern for filtering results. Specifies the relationship pattern to match when reading tuples. This can include specific values for object, user, and relation, or use partial patterns with wildcards to match multiple tuples. Empty or null values in the tuple key act as wildcards.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ReadTuplesRequestInterface.php#L90)

#### Returns

[`TupleKeyInterface`](Models/TupleKeyInterface.md) — The relationship tuple pattern for filtering results
