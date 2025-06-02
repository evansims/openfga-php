# WriteAssertionsRequestInterface

Interface for writing test assertions to an authorization model. This interface defines the contract for requests that create or update test assertions for authorization models in OpenFGA. Assertions are automated tests that verify your authorization model behaves as expected by checking specific permission scenarios against known outcomes. Assertions serve multiple important purposes: - **Testing**: Verify that your authorization model produces expected results - **Validation**: Ensure model changes don&#039;t break existing authorization logic - **Documentation**: Provide examples of how permissions should work - **Regression Prevention**: Catch unintended changes to authorization behavior - **Continuous Integration**: Enable automated testing of authorization logic Each assertion defines: - A specific permission check scenario (user, object, relation) - The expected outcome (allowed or denied) - Optional contextual data for conditional authorization Assertions are tied to specific authorization model versions, allowing you to maintain test suites that evolve with your authorization schema. When you create a new model version, you can run existing assertions to ensure backward compatibility or create new assertions for new functionality. This is essential for maintaining confidence in your authorization system as it evolves, especially in complex scenarios with inheritance, conditions, and computed relationships.

## Namespace
`OpenFGA\Requests`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Requests/WriteAssertionsRequestInterface.php)

## Implements
* [RequestInterface](RequestInterface.md)

## Related Classes
* [WriteAssertionsResponseInterface](Responses/WriteAssertionsResponseInterface.md) (response)
* [WriteAssertionsRequest](Requests/WriteAssertionsRequest.md) (implementation)



## Methods

                                                            
#### getAssertions


```php
public function getAssertions(): AssertionsInterface<AssertionInterface>
```

Get the test assertions to write to the authorization model. Returns a collection of assertions that define test scenarios for the authorization model. Each assertion specifies a permission check and its expected outcome, creating a comprehensive test suite that verifies the model&#039;s behavior across various scenarios. Assertions help ensure that: - Permission checks return expected results - Model changes don&#039;t introduce regressions - Complex authorization logic works correctly - Edge cases and special scenarios are properly handled - Documentation of expected behavior is maintained

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/WriteAssertionsRequestInterface.php#L62)


#### Returns
AssertionsInterface&lt;AssertionInterface&gt;
 Collection of test assertions to validate authorization model behavior

#### getModel


```php
public function getModel(): string
```

Get the authorization model ID to associate assertions with. Specifies which version of the authorization model these assertions should be tied to. Assertions are version-specific, allowing you to maintain different test suites for different model versions and ensure that tests remain relevant as your authorization schema evolves.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/WriteAssertionsRequestInterface.php#L74)


#### Returns
string
 The authorization model ID that these assertions will test

#### getRequest


```php
public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
```

Build a request context for HTTP execution. Transforms the request object into a standardized HTTP request context that can be executed by the OpenFGA HTTP client. This method handles all aspects of request preparation including parameter serialization, URL construction, header configuration, and body stream creation. The method validates that all required parameters are present and properly formatted, serializes complex objects to JSON, constructs the appropriate API endpoint URL, and creates the necessary HTTP message body streams.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/RequestInterface.php#L57)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$streamFactory` | StreamFactoryInterface | PSR-7 stream factory for creating request body streams from serialized data |

#### Returns
RequestContext
 The prepared request context containing HTTP method, URL, headers, and body ready for execution

#### getStore


```php
public function getStore(): string
```

Get the store ID where assertions will be written. Identifies the OpenFGA store that contains the authorization model and where the test assertions will be stored. Assertions are stored alongside the model they test, providing a complete testing framework within each store.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/WriteAssertionsRequestInterface.php#L86)


#### Returns
string
 The store ID where the test assertions will be written

