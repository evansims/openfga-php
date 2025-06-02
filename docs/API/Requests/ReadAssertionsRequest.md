# ReadAssertionsRequest

Request for reading test assertions associated with an authorization model. This request retrieves the test assertions that have been defined for an authorization model. These assertions help validate model behavior and ensure authorization logic works as expected in different scenarios.

## Namespace

`OpenFGA\Requests`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Requests/ReadAssertionsRequest.php)

## Implements

* [`ReadAssertionsRequestInterface`](ReadAssertionsRequestInterface.md)
* [`RequestInterface`](RequestInterface.md)

## Related Classes

* [ReadAssertionsResponse](Responses/ReadAssertionsResponse.md) (response)
* [ReadAssertionsRequestInterface](Requests/ReadAssertionsRequestInterface.md) (interface)

## Methods

#### getModel

```php
public function getModel(): string

```

Get the authorization model ID to read assertions from. Specifies which version of the authorization model should have its assertions retrieved. Assertions are tied to specific model versions, ensuring that tests remain relevant to the particular authorization schema they were designed to validate.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ReadAssertionsRequest.php#L56)

#### Returns

`string` — The authorization model ID whose assertions should be retrieved

#### getRequest

```php
public function getRequest(Psr\Http\Message\StreamFactoryInterface $streamFactory): OpenFGA\Network\RequestContext

```

Build a request context for HTTP execution. Transforms the request object into a standardized HTTP request context that can be executed by the OpenFGA HTTP client. This method handles all aspects of request preparation including parameter serialization, URL construction, header configuration, and body stream creation. The method validates that all required parameters are present and properly formatted, serializes complex objects to JSON, constructs the appropriate API endpoint URL, and creates the necessary HTTP message body streams.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ReadAssertionsRequest.php#L65)

#### Parameters

| Name             | Type                     | Description                                                                 |
| ---------------- | ------------------------ | --------------------------------------------------------------------------- |
| `$streamFactory` | `StreamFactoryInterface` | PSR-7 stream factory for creating request body streams from serialized data |

#### Returns

[`RequestContext`](Network/RequestContext.md) — The prepared request context containing HTTP method, URL, headers, and body ready for execution

#### getStore

```php
public function getStore(): string

```

Get the store ID containing the assertions to read. Identifies which OpenFGA store contains the authorization model and its associated test assertions. Assertions are stored alongside the models they test, providing a complete testing framework within each store&#039;s context.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ReadAssertionsRequest.php#L77)

#### Returns

`string` — The store ID containing the assertions to retrieve
