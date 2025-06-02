# WriteTuplesRequestInterface

Interface for writing relationship tuples to an OpenFGA store. This interface defines the contract for requests that modify relationship data in OpenFGA stores. It supports both adding new relationships (writes) and removing existing relationships (deletes) in a single atomic operation. Write operations are transactional, meaning either all changes succeed or all changes are rolled back. This ensures data consistency when making multiple related changes to the authorization graph. The request allows you to: - Add new relationship tuples to establish permissions - Remove existing relationship tuples to revoke permissions - Perform both operations atomically in a single request - Specify which authorization model version to use for validation

## Namespace
`OpenFGA\Requests`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Requests/WriteTuplesRequestInterface.php)

## Implements
* [`RequestInterface`](RequestInterface.md)

## Related Classes
* [WriteTuplesResponseInterface](Responses/WriteTuplesResponseInterface.md) (response)
* [WriteTuplesRequest](Requests/WriteTuplesRequest.md) (implementation)

## Methods

### CRUD Operations
#### getDeletes

```php
public function getDeletes(): TupleKeysInterface<TupleKeyInterface>|null
```

Get the relationship tuples to delete from the store. Returns a collection of relationship tuples that should be removed from the authorization store. Each tuple represents a permission or relationship that will be revoked. The deletion is atomic with any write operations specified in the same request.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/WriteTuplesRequestInterface.php#L43)

#### Returns
[`TupleKeysInterface`](Models/Collections/TupleKeysInterface.md)&lt;[`TupleKeyInterface`](Models/TupleKeyInterface.md)&gt; &#124; `null` — Collection of relationship tuples to remove, or null if no deletions are requested
#### getWrites

```php
public function getWrites(): TupleKeysInterface<TupleKeyInterface>|null
```

Get the relationship tuples to write to the store. Returns a collection of relationship tuples that should be added to the authorization store. Each tuple represents a new permission or relationship that will be granted. The write operation is atomic with any delete operations specified in the same request.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/WriteTuplesRequestInterface.php#L78)

#### Returns
[`TupleKeysInterface`](Models/Collections/TupleKeysInterface.md)&lt;[`TupleKeyInterface`](Models/TupleKeyInterface.md)&gt; &#124; `null` — Collection of relationship tuples to add, or null if no writes are requested
### List Operations
#### getModel

```php
public function getModel(): string
```

Get the authorization model ID to use for tuple validation. Specifies which version of the authorization model should be used to validate the relationship tuples being written or deleted. This ensures that all tuples conform to the expected schema and relationship types defined in the model.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/WriteTuplesRequestInterface.php#L55)

#### Returns
`string` — The authorization model ID for validating tuple operations
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
#### getStore

```php
public function getStore(): string
```

Get the store ID where tuples will be written. Identifies the OpenFGA store that contains the authorization data to be modified. All write and delete operations will be performed within the context of this specific store.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/WriteTuplesRequestInterface.php#L66)

#### Returns
`string` — The store ID containing the authorization data to modify
