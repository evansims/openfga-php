# CreateStoreRequest

Request for creating a new OpenFGA store. Stores provide data isolation for different applications or environments, maintaining separate authorization models, relationship tuples, and providing complete separation from other stores.

## Namespace
`OpenFGA\Requests`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Requests/CreateStoreRequest.php)

## Implements
* [CreateStoreRequestInterface](CreateStoreRequestInterface.md)
* [RequestInterface](RequestInterface.md)

## Related Classes
* [CreateStoreResponse](Responses/CreateStoreResponse.md) (response)
* [CreateStoreRequestInterface](Requests/CreateStoreRequestInterface.md) (interface)



## Methods

                                    
#### getName


```php
public function getName(): string
```

Get the name for the new store. Returns the human-readable name that will be assigned to the new store. This name is used for identification and administrative purposes and should be descriptive enough to distinguish the store from others in your organization. The store name: - Must be a non-empty string - Should be descriptive and meaningful for administrative purposes - Is used for display in management interfaces and logging - Does not need to be globally unique (the store ID serves that purpose)

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/CreateStoreRequest.php#L53)


#### Returns
string
 The descriptive name for the new authorization store

#### getRequest


```php
public function getRequest(Psr\Http\Message\StreamFactoryInterface $streamFactory): OpenFGA\Network\RequestContext
```

Build a request context for HTTP execution. Transforms the request object into a standardized HTTP request context that can be executed by the OpenFGA HTTP client. This method handles all aspects of request preparation including parameter serialization, URL construction, header configuration, and body stream creation. The method validates that all required parameters are present and properly formatted, serializes complex objects to JSON, constructs the appropriate API endpoint URL, and creates the necessary HTTP message body streams.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/CreateStoreRequest.php#L64)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$streamFactory` | StreamFactoryInterface | PSR-7 stream factory for creating request body streams from serialized data |

#### Returns
[RequestContext](Network/RequestContext.md)
 The prepared request context containing HTTP method, URL, headers, and body ready for execution

