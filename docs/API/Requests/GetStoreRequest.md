# GetStoreRequest

Request for retrieving store information by its ID. This request fetches the details of a specific store, including its name and metadata. It&#039;s useful for store management, displaying store information, and validating store existence before performing operations.

## Namespace
`OpenFGA\Requests`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Requests/GetStoreRequest.php)

## Implements
* [GetStoreRequestInterface](GetStoreRequestInterface.md)
* [RequestInterface](RequestInterface.md)

## Related Classes
* [GetStoreResponse](Responses/GetStoreResponse.md) (response)
* [GetStoreRequestInterface](Requests/GetStoreRequestInterface.md) (interface)



## Methods

                                    
#### getRequest


```php
public function getRequest(Psr\Http\Message\StreamFactoryInterface $streamFactory): OpenFGA\Network\RequestContext
```

Build a request context for HTTP execution. Transforms the request object into a standardized HTTP request context that can be executed by the OpenFGA HTTP client. This method handles all aspects of request preparation including parameter serialization, URL construction, header configuration, and body stream creation. The method validates that all required parameters are present and properly formatted, serializes complex objects to JSON, constructs the appropriate API endpoint URL, and creates the necessary HTTP message body streams.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/GetStoreRequest.php#L50)

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

Get the ID of the store to retrieve. Returns the unique identifier of the store whose information should be fetched. This will return metadata about the store including its name, creation timestamp, and other administrative details.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/GetStoreRequest.php#L62)


#### Returns
string
 The unique identifier of the store to retrieve information for

