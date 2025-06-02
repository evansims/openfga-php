# ReadAssertionsResponseInterface

Interface for assertions reading response objects. This interface defines the contract for responses returned when reading assertions from an OpenFGA authorization model. Assertions are test cases that validate the behavior of an authorization model by specifying expected permission check results. Assertion reading is used for testing authorization models, validating model behavior, and ensuring that permission logic works as expected during development and deployment.

## Namespace

`OpenFGA\Responses`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Responses/ReadAssertionsResponseInterface.php)

## Implements

* [`ResponseInterface`](ResponseInterface.md)

## Related Classes

* [ReadAssertionsResponse](Responses/ReadAssertionsResponse.md) (implementation)
* [ReadAssertionsRequestInterface](Requests/ReadAssertionsRequestInterface.md) (request)

## Methods

#### getAssertions

```php
public function getAssertions(): AssertionsInterface<AssertionInterface>|null

```

Get the collection of assertions from the authorization model. Returns a type-safe collection containing the assertion objects associated with the authorization model. Each assertion defines a test case with expected permission check results for validating model behavior.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/ReadAssertionsResponseInterface.php#L46)

#### Returns

[`AssertionsInterface`](Models/Collections/AssertionsInterface.md)&lt;[`AssertionInterface`](Models/AssertionInterface.md)&gt; &#124; `null` — The collection of assertions, or null if no assertions are defined

#### getModel

```php
public function getModel(): string

```

Get the authorization model identifier for these assertions. Returns the unique identifier of the authorization model that contains these assertions. This ties the assertions to a specific model version for validation and testing purposes.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/ReadAssertionsResponseInterface.php#L57)

#### Returns

`string` — The authorization model identifier
