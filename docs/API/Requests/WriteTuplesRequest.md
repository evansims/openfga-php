# WriteTuplesRequest

Request for writing and deleting relationship tuples in OpenFGA. This request enables batch creation and deletion of relationship tuples, allowing you to efficiently manage user-object relationships in a single atomic operation. All changes are applied transactionally.

## Namespace
`OpenFGA\Requests`

## Implements
* [WriteTuplesRequestInterface](Requests/WriteTuplesRequestInterface.md)
* [RequestInterface](Requests/RequestInterface.md)



## Methods
### getDeletes


```php
public function getDeletes(): ?OpenFGA\Models\Collections\TupleKeysInterface
```

Get the relationship tuples to delete from the store. Returns a collection of relationship tuples that should be removed from the authorization store. Each tuple represents a permission or relationship that will be revoked. The deletion is atomic with any write operations specified in the same request.


#### Returns
?[TupleKeysInterface](Models/Collections/TupleKeysInterface.md)
 Collection of relationship tuples to remove, or null if no deletions are requested

### getModel


```php
public function getModel(): string
```

Get the authorization model ID to use for tuple validation. Specifies which version of the authorization model should be used to validate the relationship tuples being written or deleted. This ensures that all tuples conform to the expected schema and relationship types defined in the model.


#### Returns
string
 The authorization model ID for validating tuple operations

### getRequest


```php
public function getRequest(Psr\Http\Message\StreamFactoryInterface $streamFactory): OpenFGA\Network\RequestContext
```

Build a request context for HTTP execution. Transforms the request object into a standardized HTTP request context that can be executed by the OpenFGA HTTP client. This method handles all aspects of request preparation including parameter serialization, URL construction, header configuration, and body stream creation. The method validates that all required parameters are present and properly formatted, serializes complex objects to JSON, constructs the appropriate API endpoint URL, and creates the necessary HTTP message body streams.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$streamFactory` | StreamFactoryInterface | PSR-7 stream factory for creating request body streams from serialized data |

#### Returns
[RequestContext](Network/RequestContext.md)
 The prepared request context containing HTTP method, URL, headers, and body ready for execution

### getStore


```php
public function getStore(): string
```

Get the store ID where tuples will be written. Identifies the OpenFGA store that contains the authorization data to be modified. All write and delete operations will be performed within the context of this specific store.


#### Returns
string
 The store ID containing the authorization data to modify

### getWrites


```php
public function getWrites(): ?OpenFGA\Models\Collections\TupleKeysInterface
```

Get the relationship tuples to write to the store. Returns a collection of relationship tuples that should be added to the authorization store. Each tuple represents a new permission or relationship that will be granted. The write operation is atomic with any delete operations specified in the same request.


#### Returns
?[TupleKeysInterface](Models/Collections/TupleKeysInterface.md)
 Collection of relationship tuples to add, or null if no writes are requested

