# ReadAssertionsRequestInterface

Interface for reading test assertions from an authorization model. This interface defines the contract for requests that retrieve test assertions associated with a specific authorization model. Assertions are automated tests that verify authorization model behavior by checking specific permission scenarios against expected outcomes. Reading assertions is essential for: - **Test Execution**: Running automated tests to verify model behavior - **Model Validation**: Ensuring authorization logic works as expected - **Debugging**: Understanding test scenarios when troubleshooting issues - **Documentation**: Reviewing examples of how permissions should work - **Continuous Integration**: Automating authorization model testing - **Regression Testing**: Verifying that model changes don&#039;t break existing behavior The retrieved assertions include the test scenarios, expected outcomes, and any contextual data needed to execute the tests. This provides a complete test suite that can be run to validate the authorization model&#039;s correctness.

## Namespace
`OpenFGA\Requests`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Requests/ReadAssertionsRequestInterface.php)

## Implements
* [`RequestInterface`](RequestInterface.md)

## Related Classes
* [ReadAssertionsResponseInterface](Responses/ReadAssertionsResponseInterface.md) (response)
* [ReadAssertionsRequest](Requests/ReadAssertionsRequest.md) (implementation)

## Methods

#### getModel

```php
public function getModel(): string
```

Get the authorization model ID to read assertions from. Specifies which version of the authorization model should have its assertions retrieved. Assertions are tied to specific model versions, ensuring that tests remain relevant to the particular authorization schema they were designed to validate.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ReadAssertionsRequestInterface.php#L42)

#### Returns
`string` — The authorization model ID whose assertions should be retrieved
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

Get the store ID containing the assertions to read. Identifies which OpenFGA store contains the authorization model and its associated test assertions. Assertions are stored alongside the models they test, providing a complete testing framework within each store&#039;s context.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ReadAssertionsRequestInterface.php#L54)

#### Returns
`string` — The store ID containing the assertions to retrieve
