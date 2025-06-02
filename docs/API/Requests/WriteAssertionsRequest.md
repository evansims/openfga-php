# WriteAssertionsRequest

Request for writing test assertions to validate authorization model behavior. This request stores test assertions that define expected authorization outcomes for specific scenarios. Assertions are used to validate that authorization models behave correctly and can be run as part of testing and validation workflows.

## Namespace
`OpenFGA\Requests`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Requests/WriteAssertionsRequest.php)

## Implements
* [`WriteAssertionsRequestInterface`](WriteAssertionsRequestInterface.md)
* [`RequestInterface`](RequestInterface.md)

## Related Classes
* [WriteAssertionsResponse](Responses/WriteAssertionsResponse.md) (response)
* [WriteAssertionsRequestInterface](Requests/WriteAssertionsRequestInterface.md) (interface)

## Methods

#### getAssertions

```php
public function getAssertions(): OpenFGA\Models\Collections\AssertionsInterface
```

Get the test assertions to write to the authorization model. Returns a collection of assertions that define test scenarios for the authorization model. Each assertion specifies a permission check and its expected outcome, creating a comprehensive test suite that verifies the model&#039;s behavior across various scenarios. Assertions help ensure that: - Permission checks return expected results - Model changes don&#039;t introduce regressions - Complex authorization logic works correctly - Edge cases and special scenarios are properly handled - Documentation of expected behavior is maintained

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/WriteAssertionsRequest.php#L62)

#### Returns
[`AssertionsInterface`](Models/Collections/AssertionsInterface.md) — Collection of test assertions to validate authorization model behavior
#### getModel

```php
public function getModel(): string
```

Get the authorization model ID to associate assertions with. Specifies which version of the authorization model these assertions should be tied to. Assertions are version-specific, allowing you to maintain different test suites for different model versions and ensure that tests remain relevant as your authorization schema evolves.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/WriteAssertionsRequest.php#L71)

#### Returns
`string` — The authorization model ID that these assertions will test
#### getRequest

```php
public function getRequest(Psr\Http\Message\StreamFactoryInterface $streamFactory): OpenFGA\Network\RequestContext
```

Build a request context for HTTP execution. Transforms the request object into a standardized HTTP request context that can be executed by the OpenFGA HTTP client. This method handles all aspects of request preparation including parameter serialization, URL construction, header configuration, and body stream creation. The method validates that all required parameters are present and properly formatted, serializes complex objects to JSON, constructs the appropriate API endpoint URL, and creates the necessary HTTP message body streams.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/WriteAssertionsRequest.php#L82)

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

Get the store ID where assertions will be written. Identifies the OpenFGA store that contains the authorization model and where the test assertions will be stored. Assertions are stored alongside the model they test, providing a complete testing framework within each store.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/WriteAssertionsRequest.php#L99)

#### Returns
`string` — The store ID where the test assertions will be written
