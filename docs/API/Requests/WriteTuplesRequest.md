# WriteTuplesRequest

Request for writing and deleting relationship tuples in OpenFGA. This request enables batch creation and deletion of relationship tuples, supporting both transactional (all-or-nothing) and non-transactional (independent operations) modes. Transactional mode ensures atomic changes, while non-transactional mode allows for parallel processing with detailed success/failure tracking.

## Table of Contents

* [Namespace](#namespace)
* [Source](#source)
* [Implements](#implements)
* [Related Classes](#related-classes)
* [Methods](#methods)

* [CRUD Operations](#crud-operations)
    * [`getDeletes()`](#getdeletes)
    * [`getWrites()`](#getwrites)
* [List Operations](#list-operations)
    * [`getMaxParallelRequests()`](#getmaxparallelrequests)
    * [`getMaxRetries()`](#getmaxretries)
    * [`getMaxTuplesPerChunk()`](#getmaxtuplesperchunk)
    * [`getModel()`](#getmodel)
    * [`getRequest()`](#getrequest)
    * [`getRetryDelaySeconds()`](#getretrydelayseconds)
    * [`getStopOnFirstError()`](#getstoponfirsterror)
    * [`getStore()`](#getstore)
    * [`getTotalOperations()`](#gettotaloperations)
* [Utility](#utility)
    * [`isEmpty()`](#isempty)
    * [`isTransactional()`](#istransactional)
* [Other](#other)
    * [`chunk()`](#chunk)

## Namespace

`OpenFGA\Requests`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Requests/WriteTuplesRequest.php)

## Implements

* [`WriteTuplesRequestInterface`](WriteTuplesRequestInterface.md)
* [`RequestInterface`](RequestInterface.md)

## Related Classes

* [WriteTuplesResponse](Responses/WriteTuplesResponse.md) (response)
* [WriteTuplesRequestInterface](Requests/WriteTuplesRequestInterface.md) (interface)

## Methods

### CRUD Operations

#### getDeletes

```php
public function getDeletes(): ?OpenFGA\Models\Collections\TupleKeysInterface

```

Get the relationship tuples to delete from the store. Returns a collection of relationship tuples that should be removed from the authorization store. Each tuple represents a permission or relationship that will be revoked. The deletion is atomic with any write operations specified in the same request.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/WriteTuplesRequest.php#L165)

#### Returns

[`TupleKeysInterface`](Models/Collections/TupleKeysInterface.md) &#124; `null` — Collection of relationship tuples to remove, or null if no deletions are requested

#### getWrites

```php
public function getWrites(): ?OpenFGA\Models\Collections\TupleKeysInterface

```

Get the relationship tuples to write to the store. Returns a collection of relationship tuples that should be added to the authorization store. Each tuple represents a new permission or relationship that will be granted. The write operation is atomic with any delete operations specified in the same request.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/WriteTuplesRequest.php#L273)

#### Returns

[`TupleKeysInterface`](Models/Collections/TupleKeysInterface.md) &#124; `null` — Collection of relationship tuples to add, or null if no writes are requested

### List Operations

#### getMaxParallelRequests

```php
public function getMaxParallelRequests(): int

```

Get the maximum number of parallel requests for non-transactional mode.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/WriteTuplesRequest.php#L174)

#### Returns

`int` — Maximum parallel requests (1 for sequential processing)

#### getMaxRetries

```php
public function getMaxRetries(): int

```

Get the maximum number of retries for failed chunks in non-transactional mode.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/WriteTuplesRequest.php#L183)

#### Returns

`int` — Maximum retry attempts

#### getMaxTuplesPerChunk

```php
public function getMaxTuplesPerChunk(): int

```

Get the maximum number of tuples per chunk for non-transactional mode.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/WriteTuplesRequest.php#L192)

#### Returns

`int` — Maximum tuples per chunk (up to 100)

#### getModel

```php
public function getModel(): string

```

Get the authorization model ID to use for tuple validation. Specifies which version of the authorization model should be used to validate the relationship tuples being written or deleted. This ensures that all tuples conform to the expected schema and relationship types defined in the model.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/WriteTuplesRequest.php#L201)

#### Returns

`string` — The authorization model ID for validating tuple operations

#### getRequest

```php
public function getRequest(Psr\Http\Message\StreamFactoryInterface $streamFactory): OpenFGA\Network\RequestContext

```

Build a request context for HTTP execution. Transforms the request object into a standardized HTTP request context that can be executed by the OpenFGA HTTP client. This method handles all aspects of request preparation including parameter serialization, URL construction, header configuration, and body stream creation. The method validates that all required parameters are present and properly formatted, serializes complex objects to JSON, constructs the appropriate API endpoint URL, and creates the necessary HTTP message body streams.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/WriteTuplesRequest.php#L212)

#### Parameters

| Name             | Type                     | Description                                                                 |
| ---------------- | ------------------------ | --------------------------------------------------------------------------- |
| `$streamFactory` | `StreamFactoryInterface` | PSR-7 stream factory for creating request body streams from serialized data |

#### Returns

[`RequestContext`](Network/RequestContext.md) — The prepared request context containing HTTP method, URL, headers, and body ready for execution

#### getRetryDelaySeconds

```php
public function getRetryDelaySeconds(): float

```

Get the retry delay in seconds for non-transactional mode.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/WriteTuplesRequest.php#L233)

#### Returns

`float` — Retry delay in seconds

#### getStopOnFirstError

```php
public function getStopOnFirstError(): bool

```

Check if non-transactional processing should stop on first error.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/WriteTuplesRequest.php#L242)

#### Returns

`bool` — True to stop on first error, false to continue

#### getStore

```php
public function getStore(): string

```

Get the store ID where tuples will be written. Identifies the OpenFGA store that contains the authorization data to be modified. All write and delete operations will be performed within the context of this specific store.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/WriteTuplesRequest.php#L251)

#### Returns

`string` — The store ID containing the authorization data to modify

#### getTotalOperations

```php
public function getTotalOperations(): int

```

Get the total number of tuple operations in this request.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/WriteTuplesRequest.php#L261)

#### Returns

`int` — Total count of write and delete operations

### Utility

#### isEmpty

```php
public function isEmpty(): bool

```

Check if this request contains any operations.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/WriteTuplesRequest.php#L283)

#### Returns

`bool` — True if the request has no operations

#### isTransactional

```php
public function isTransactional(): bool

```

Check if this request should be executed in transactional mode.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/WriteTuplesRequest.php#L292)

#### Returns

`bool` — True for transactional mode, false for non-transactional

### Other

#### chunk

```php
public function chunk(int $chunkSize): array<WriteTuplesRequest>

```

Split this request into smaller chunks for non-transactional processing.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/WriteTuplesRequest.php#L95)

#### Parameters

| Name         | Type  | Description                      |
| ------------ | ----- | -------------------------------- |
| `$chunkSize` | `int` | Maximum tuples per chunk (1-100) |

#### Returns

`array&lt;[`WriteTuplesRequest`](WriteTuplesRequest.md)&gt;` — Array of chunked requests
