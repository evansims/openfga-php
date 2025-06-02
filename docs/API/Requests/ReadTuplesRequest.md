# ReadTuplesRequest

Request for reading relationship tuples that match specified criteria. This request retrieves tuples from a store based on filtering criteria, with support for pagination and consistency levels. It&#039;s essential for querying existing relationships, debugging authorization data, and building administrative interfaces.

## Namespace
`OpenFGA\Requests`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Requests/ReadTuplesRequest.php)

## Implements
* [ReadTuplesRequestInterface](ReadTuplesRequestInterface.md)
* [RequestInterface](RequestInterface.md)

## Related Classes
* [ReadTuplesResponse](Responses/ReadTuplesResponse.md) (response)
* [ReadTuplesRequestInterface](Requests/ReadTuplesRequestInterface.md) (interface)



## Methods

                                                                                    
#### getConsistency


```php
public function getConsistency(): ?OpenFGA\Models\Enums\Consistency
```

Get the read consistency level for the read operation. Determines the consistency guarantees for reading relationship tuples. This allows you to balance between read performance and data freshness based on your application&#039;s requirements.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ReadTuplesRequest.php#L71)


#### Returns
?Consistency
 The consistency level for the operation, or null to use the default consistency setting

#### getContinuationToken


```php
public function getContinuationToken(): ?string
```

Get the continuation token for paginated results. Returns the pagination token from a previous read operation to continue retrieving results from where the last request left off. This enables efficient pagination through large result sets without missing or duplicating tuples.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ReadTuplesRequest.php#L80)


#### Returns
?string
 The continuation token from a previous read operation, or null for the first page

#### getPageSize


```php
public function getPageSize(): ?int
```

Get the maximum number of tuples to return. Specifies the page size for paginated results. This controls how many relationship tuples are returned in a single response. Smaller page sizes reduce memory usage and latency, while larger page sizes reduce the number of API calls needed for large datasets.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ReadTuplesRequest.php#L89)


#### Returns
?int
 The maximum number of tuples to return per page, or null to use the default page size

#### getRequest


```php
public function getRequest(Psr\Http\Message\StreamFactoryInterface $streamFactory): OpenFGA\Network\RequestContext
```

Build a request context for HTTP execution. Transforms the request object into a standardized HTTP request context that can be executed by the OpenFGA HTTP client. This method handles all aspects of request preparation including parameter serialization, URL construction, header configuration, and body stream creation. The method validates that all required parameters are present and properly formatted, serializes complex objects to JSON, constructs the appropriate API endpoint URL, and creates the necessary HTTP message body streams.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ReadTuplesRequest.php#L100)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$streamFactory` | StreamFactoryInterface | PSR-7 stream factory for creating request body streams from serialized data |

#### Returns
[RequestContext](Network/RequestContext.md)
 The prepared request context containing HTTP method, URL, headers, and body ready for execution

#### getStore


```php
public function getStore(): string
```

Get the store ID containing the tuples to read. Identifies which OpenFGA store contains the relationship tuples to query. All read operations will be performed within the context of this specific store, ensuring data isolation from other stores.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ReadTuplesRequest.php#L122)


#### Returns
string
 The store ID containing the relationship tuples to read

#### getTupleKey


```php
public function getTupleKey(): OpenFGA\Models\TupleKeyInterface
```

Get the tuple key pattern for filtering results. Specifies the relationship pattern to match when reading tuples. This can include specific values for object, user, and relation, or use partial patterns with wildcards to match multiple tuples. Empty or null values in the tuple key act as wildcards.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ReadTuplesRequest.php#L131)


#### Returns
[TupleKeyInterface](Models/TupleKeyInterface.md)
 The relationship tuple pattern for filtering results

