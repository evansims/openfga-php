# DeleteStoreRequest

Request for permanently deleting a store and all its data. This request removes the entire store, including all authorization models, relationship tuples, and associated metadata. This operation is irreversible and should be used with extreme caution in production environments.

## Namespace
`OpenFGA\Requests`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Requests/DeleteStoreRequest.php)

## Implements
* [`DeleteStoreRequestInterface`](DeleteStoreRequestInterface.md)
* [`RequestInterface`](RequestInterface.md)

## Related Classes
* [DeleteStoreResponse](Responses/DeleteStoreResponse.md) (response)
* [DeleteStoreRequestInterface](Requests/DeleteStoreRequestInterface.md) (interface)



## Methods

                                    
#### getRequest


```php
public function getRequest(Psr\Http\Message\StreamFactoryInterface $streamFactory): OpenFGA\Network\RequestContext
```

Build a request context for HTTP execution. Transforms the request object into a standardized HTTP request context that can be executed by the OpenFGA HTTP client. This method handles all aspects of request preparation including parameter serialization, URL construction, header configuration, and body stream creation. The method validates that all required parameters are present and properly formatted, serializes complex objects to JSON, constructs the appropriate API endpoint URL, and creates the necessary HTTP message body streams.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/DeleteStoreRequest.php#L50)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$streamFactory` | `StreamFactoryInterface` | PSR-7 stream factory for creating request body streams from serialized data |

#### Returns
[`RequestContext`](Network/RequestContext.md) — The prepared request context containing HTTP method, URL, headers, and body ready for execution
#### getStore


```php
public function getStore(): string
```

Get the ID of the store to delete. Returns the unique identifier of the store that will be permanently removed from OpenFGA. This operation will delete all data associated with the store, including relationship tuples, authorization models, and configuration settings. Important:** This is a destructive operation that cannot be reversed. Ensure you have the correct store ID and proper authorization before proceeding with the deletion.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/DeleteStoreRequest.php#L62)


#### Returns
`string` — The unique identifier of the store to permanently delete
