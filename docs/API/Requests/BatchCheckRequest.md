# BatchCheckRequest

Request for performing multiple authorization checks in a single batch. This request allows checking multiple user-object relationships simultaneously for better performance when multiple authorization decisions are needed. Each check in the batch has a correlation ID to map results back to the original requests.

## Namespace
`OpenFGA\Requests`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Requests/BatchCheckRequest.php)

## Implements
* [BatchCheckRequestInterface](BatchCheckRequestInterface.md)
* [RequestInterface](RequestInterface.md)

## Related Classes
* [BatchCheckResponse](Responses/BatchCheckResponse.md) (response)
* [BatchCheckRequestInterface](Requests/BatchCheckRequestInterface.md) (interface)



## Methods

                                                
### Authorization
#### getChecks


```php
public function getChecks(): OpenFGA\Models\Collections\BatchCheckItemsInterface
```

Get the collection of checks to perform in this batch. Each item contains a tuple key to check and a correlation ID to map the result back to this specific check.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/BatchCheckRequest.php#L63)


#### Returns
[BatchCheckItemsInterface](Models/Collections/BatchCheckItemsInterface.md)
 The batch check items

### List Operations
#### getRequest


```php
public function getRequest(Psr\Http\Message\StreamFactoryInterface $streamFactory): OpenFGA\Network\RequestContext
```

Build a request context for HTTP execution. Transforms the request object into a standardized HTTP request context that can be executed by the OpenFGA HTTP client. This method handles all aspects of request preparation including parameter serialization, URL construction, header configuration, and body stream creation. The method validates that all required parameters are present and properly formatted, serializes complex objects to JSON, constructs the appropriate API endpoint URL, and creates the necessary HTTP message body streams.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/BatchCheckRequest.php#L72)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$streamFactory` | StreamFactoryInterface | PSR-7 stream factory for creating request body streams from serialized data |

#### Returns
[RequestContext](Network/RequestContext.md)
 The prepared request context containing HTTP method, URL, headers, and body ready for execution

